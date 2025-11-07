<?php
namespace App\Services;

use App\Support\PatientOptions;
use RuntimeException;

class LocalPatientStore
{
    private string $filePath;

    public function __construct(?string $filePath = null)
    {
        if ($filePath !== null) {
            $this->filePath = $filePath;
        } else {
            $baseDir = dirname(__DIR__, 2) . '/storage/data';
            $defaultPath = $baseDir . '/admin_patients.json';
            $legacyPath = $baseDir . '/admin_users.json';
            $this->filePath = file_exists($defaultPath) || !file_exists($legacyPath)
                ? $defaultPath
                : $legacyPath;
        }
        $this->ensureStorage();
    }

    /**
     * Retorna todos os pacientes ordenados pelo nome.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $patients = $this->load();

        usort($patients, static function (array $a, array $b): int {
            return strcasecmp($a['name'] ?? '', $b['name'] ?? '');
        });

        return $patients;
    }

    public function find(int $id): ?array
    {
        foreach ($this->load() as $patient) {
            if ((int)($patient['id'] ?? 0) === $id) {
                return $patient;
            }
        }

        return null;
    }

    public function create(array $payload): array
    {
        $patients = $this->load();
        $nextId = $this->nextId($patients);

        $patient = [
            'id' => $nextId,
            'name' => $payload['name'],
            'email' => $payload['email'],
            'convenio' => PatientOptions::normalizeConvenio($payload['convenio'] ?? null),
            'situacao_cadastro' => PatientOptions::normalizeSituacao($payload['situacao_cadastro'] ?? null),
            'avatar' => $this->normalizeAvatar($payload['avatar'] ?? null),
            'birth_date' => $this->normalizeDate($payload['birth_date'] ?? null),
            'cpf' => $this->normalizeCpf($payload['cpf'] ?? ''),
            'address' => $this->normalizeText($payload['address'] ?? ''),
            'staff_id' => $this->generateStaffId($patients),
            'created_at' => $payload['created_at'] ?? date('c'),
            'updated_at' => $payload['updated_at'] ?? date('c'),
        ];

        $patients[] = $patient;
        $this->persist($patients);

        return $patient;
    }

    public function update(int $id, array $payload): bool
    {
        $patients = $this->load();
        $updated = false;

        foreach ($patients as &$patient) {
            if ((int)($patient['id'] ?? 0) === $id) {
                $patient['name'] = $payload['name'];
                $patient['email'] = $payload['email'];
                $patient['convenio'] = PatientOptions::normalizeConvenio($payload['convenio'] ?? ($patient['convenio'] ?? null));
                $patient['situacao_cadastro'] = PatientOptions::normalizeSituacao($payload['situacao_cadastro'] ?? ($patient['situacao_cadastro'] ?? null));
                $patient['avatar'] = $this->normalizeAvatar($payload['avatar'] ?? $patient['avatar'] ?? null);
                $patient['birth_date'] = $this->normalizeDate($payload['birth_date'] ?? $patient['birth_date'] ?? null);
                $patient['cpf'] = $this->normalizeCpf($payload['cpf'] ?? $patient['cpf'] ?? '');
                $patient['address'] = $this->normalizeText($payload['address'] ?? $patient['address'] ?? '');
                $patient['staff_id'] = $patient['staff_id'] ?? $this->generateStaffId($patients);
                $patient['updated_at'] = date('c');
                $updated = true;
                break;
            }
        }
        unset($patient);

        if ($updated) {
            $this->persist($patients);
        }

        return $updated;
    }

    public function delete(int $id): bool
    {
        $patients = $this->load();
        $filtered = array_filter($patients, static fn(array $patient): bool => (int)($patient['id'] ?? 0) !== $id);

        if (count($patients) === count($filtered)) {
            return false;
        }

        $this->persist(array_values($filtered));
        return true;
    }

    public function emailExists(string $email, ?int $ignoreId = null): bool
    {
        foreach ($this->load() as $patient) {
            if ($ignoreId !== null && (int)($patient['id'] ?? 0) === $ignoreId) {
                continue;
            }
            if (strcasecmp($patient['email'] ?? '', $email) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function load(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $contents = file_get_contents($this->filePath);
        if ($contents === false || $contents === '') {
            return [];
        }

        $decoded = json_decode($contents, true);
        if (!is_array($decoded)) {
            return [];
        }

        $changed = false;
        $existingIds = [];
        foreach ($decoded as $entry) {
            if (!empty($entry['staff_id'])) {
                $existingIds[] = (string)$entry['staff_id'];
            }
        }

        foreach ($decoded as &$entry) {
            if (empty($entry['staff_id'])) {
                $entry['staff_id'] = $this->generateStaffId($decoded, $existingIds);
                $existingIds[] = $entry['staff_id'];
                $changed = true;
            }

            if (!isset($entry['convenio']) && isset($entry['role'])) {
                $entry['convenio'] = PatientOptions::normalizeConvenio($entry['role']);
                unset($entry['role']);
                $changed = true;
            } else {
                $entry['convenio'] = PatientOptions::normalizeConvenio($entry['convenio'] ?? null);
            }

            if (!isset($entry['situacao_cadastro']) && isset($entry['status'])) {
                $entry['situacao_cadastro'] = PatientOptions::normalizeSituacao($entry['status']);
                unset($entry['status']);
                $changed = true;
            } else {
                $entry['situacao_cadastro'] = PatientOptions::normalizeSituacao($entry['situacao_cadastro'] ?? null);
            }
        }
        unset($entry);

        if ($changed) {
            $this->persist($decoded);
        }

        return $decoded;
    }

    private function persist(array $patients): void
    {
        $encoded = json_encode($patients, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($encoded === false) {
            throw new RuntimeException('Falha ao serializar pacientes.');
        }

        $result = file_put_contents($this->filePath, $encoded, LOCK_EX);
        if ($result === false) {
            throw new RuntimeException('Não foi possível gravar os pacientes no armazenamento local.');
        }
    }

    private function nextId(array $patients): int
    {
        $max = 0;
        foreach ($patients as $patient) {
            $id = (int)($patient['id'] ?? 0);
            if ($id > $max) {
                $max = $id;
            }
        }

        return $max + 1;
    }

    private function ensureStorage(): void
    {
        $directory = dirname($this->filePath);
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0775, true) && !is_dir($directory)) {
                throw new RuntimeException(sprintf('Não foi possível criar o diretório de armazenamento: %s', $directory));
            }
        }

        if (!file_exists($this->filePath)) {
            $this->persist([
                [
                    'id' => 1,
                    'name' => 'Paciente Demonstração',
                    'email' => 'paciente@meuapp.local',
                    'convenio' => PatientOptions::defaultConvenio(),
                    'situacao_cadastro' => PatientOptions::defaultSituacao(),
                    'avatar' => null,
                    'birth_date' => null,
                    'cpf' => '',
                    'address' => '',
                    'staff_id' => 'PAT' . date('Ymd') . '0001',
                    'created_at' => date('c'),
                    'updated_at' => date('c'),
                ],
            ]);
        }
    }

    private function normalizeAvatar(?string $avatar): ?string
    {
        if ($avatar === null) {
            return null;
        }

        $avatar = trim($avatar);
        if ($avatar === '') {
            return null;
        }

        if (
            str_starts_with($avatar, 'http://') ||
            str_starts_with($avatar, 'https://') ||
            str_starts_with($avatar, '/')
        ) {
            return $avatar;
        }

        return null;
    }

    private function normalizeDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        $timestamp = strtotime(str_replace('/', '-', trim($date)));
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d', $timestamp);
    }

    private function normalizeCpf(string $cpf): string
    {
        $digits = preg_replace('/\D+/', '', $cpf);
        return $digits ?? '';
    }

    private function normalizeText(string $text): string
    {
        return trim($text);
    }

    private function generateStaffId(array $patients, array $existing = []): string
    {
        do {
            $candidate = $this->randomStaffId();
        } while (in_array($candidate, $existing, true) || $this->existsIn($patients, $candidate));

        return $candidate;
    }

    private function existsIn(array $patients, string $staffId): bool
    {
        foreach ($patients as $patient) {
            if (($patient['staff_id'] ?? null) === $staffId) {
                return true;
            }
        }

        return false;
    }

    private function randomStaffId(): string
    {
        try {
            return 'PAT' . strtoupper(bin2hex(random_bytes(3)));
        } catch (\Throwable) {
            return 'PAT' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 6));
        }
    }
}
