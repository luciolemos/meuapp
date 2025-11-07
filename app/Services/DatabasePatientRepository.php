<?php
namespace App\Services;

use App\Support\Database;
use App\Support\PatientOptions;
use PDO;

class DatabasePatientRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM tbl_meuapp_patients ORDER BY name');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tbl_meuapp_patients WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user !== false ? $user : null;
    }

    public function create(array $payload): array
    {
        $staffId = !empty($payload['staff_id']) ? $payload['staff_id'] : $this->generateStaffId();

        if (!$this->isUniqueStaffId($staffId)) {
            $staffId = $this->generateStaffId();
        }

        $sql = 'INSERT INTO tbl_meuapp_patients
                (staff_id, name, email, convenio, situacao_cadastro, avatar, birth_date, cpf, address, created_at, updated_at)
                VALUES
                (:staff_id, :name, :email, :convenio, :situacao_cadastro, :avatar, :birth_date, :cpf, :address, NOW(), NOW())';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'staff_id' => $staffId,
            'name' => $payload['name'],
            'email' => $payload['email'],
            'convenio' => PatientOptions::normalizeConvenio($payload['convenio'] ?? null),
            'situacao_cadastro' => PatientOptions::normalizeSituacao($payload['situacao_cadastro'] ?? null),
            'avatar' => $this->normalizeAvatar($payload['avatar'] ?? null),
            'birth_date' => $this->normalizeDate($payload['birth_date'] ?? null),
            'cpf' => $this->normalizeCpf($payload['cpf'] ?? ''),
            'address' => $this->normalizeText($payload['address'] ?? ''),
        ]);

        return $this->find((int)$this->pdo->lastInsertId()) ?? [];
    }

    public function update(int $id, array $payload): bool
    {
        $sql = 'UPDATE tbl_meuapp_patients SET
                    name = :name,
                    email = :email,
                    convenio = :convenio,
                    situacao_cadastro = :situacao_cadastro,
                    avatar = :avatar,
                    birth_date = :birth_date,
                    cpf = :cpf,
                    address = :address,
                    updated_at = NOW()
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'convenio' => PatientOptions::normalizeConvenio($payload['convenio'] ?? null),
            'situacao_cadastro' => PatientOptions::normalizeSituacao($payload['situacao_cadastro'] ?? null),
            'avatar' => $this->normalizeAvatar($payload['avatar'] ?? null),
            'birth_date' => $this->normalizeDate($payload['birth_date'] ?? null),
            'cpf' => $this->normalizeCpf($payload['cpf'] ?? ''),
            'address' => $this->normalizeText($payload['address'] ?? ''),
            'id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM tbl_meuapp_patients WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function emailExists(string $email, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT id FROM tbl_meuapp_patients WHERE email = :email';
        $params = ['email' => $email];

        if ($ignoreId !== null) {
            $sql .= ' AND id <> :ignore';
            $params['ignore'] = $ignoreId;
        }

        $sql .= ' LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch() !== false;
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

        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://') || str_starts_with($avatar, '/')) {
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

    private function generateStaffId(): string
    {
        do {
            try {
                $random = strtoupper(bin2hex(random_bytes(2)));
            } catch (\Throwable $e) {
                $random = strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 4));
            }
            $candidate = 'PAT' . date('YmdHis') . $random;
        } while (!$this->isUniqueStaffId($candidate));

        return $candidate;
    }

    private function isUniqueStaffId(string $staffId): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM tbl_meuapp_patients WHERE staff_id = :staff LIMIT 1');
        $stmt->execute(['staff' => $staffId]);
        return $stmt->fetch() === false;
    }
}
