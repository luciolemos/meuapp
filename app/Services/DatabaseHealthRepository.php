<?php
namespace App\Services;

use App\Support\Database;
use PDO;

class DatabaseHealthRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    public function all(?int $userId = null): array
    {
        $sql = 'SELECT h.*,
                       h.user_id    AS patient_id,
                       u.name       AS patient_name,
                       u.email      AS patient_email,
                       u.staff_id   AS patient_staff_id,
                       u.last_health_state AS patient_last_health_state,
                       u.last_consulta_em  AS patient_last_consulta_em
                  FROM tbl_meuapp_health h
                  INNER JOIN tbl_meuapp_patients u ON u.id = h.user_id';

        $params = [];
        if ($userId !== null) {
            $sql .= ' WHERE h.user_id = :user_id';
            $params['user_id'] = $userId;
        }

        $sql .= ' ORDER BY h.consulta_data DESC, h.id DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $sql = 'SELECT h.*,
                       h.user_id    AS patient_id,
                       u.name     AS patient_name,
                       u.email    AS patient_email,
                       u.staff_id AS patient_staff_id
                  FROM tbl_meuapp_health h
                  INNER JOIN tbl_meuapp_patients u ON u.id = h.user_id
                 WHERE h.id = :id
                 LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    public function create(array $payload): array
    {
        $sql = 'INSERT INTO tbl_meuapp_health
                    (user_id, consulta_data, estado, diagnostico, cid, tratamento, prescricoes,
                     observacoes, medico_responsavel, instituicao, pressao_arterial,
                     frequencia_cardiaca, temperatura_c, peso_kg, altura_m,
                     proxima_consulta, status_tratamento, created_at, updated_at)
                 VALUES
                    (:user_id, :consulta_data, :estado, :diagnostico, :cid, :tratamento, :prescricoes,
                     :observacoes, :medico_responsavel, :instituicao, :pressao_arterial,
                     :frequencia_cardiaca, :temperatura_c, :peso_kg, :altura_m,
                     :proxima_consulta, :status_tratamento, NOW(), NOW())';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->hydrateParams($payload));

        $id = (int)$this->pdo->lastInsertId();
        $record = $this->find($id) ?? [];

        $this->refreshPatientSummary((int)$payload['user_id']);

        return $record;
    }

    public function update(int $id, array $payload): bool
    {
        $sql = 'UPDATE tbl_meuapp_health SET
                    user_id = :user_id,
                    consulta_data = :consulta_data,
                    estado = :estado,
                    diagnostico = :diagnostico,
                    cid = :cid,
                    tratamento = :tratamento,
                    prescricoes = :prescricoes,
                    observacoes = :observacoes,
                    medico_responsavel = :medico_responsavel,
                    instituicao = :instituicao,
                    pressao_arterial = :pressao_arterial,
                    frequencia_cardiaca = :frequencia_cardiaca,
                    temperatura_c = :temperatura_c,
                    peso_kg = :peso_kg,
                    altura_m = :altura_m,
                    proxima_consulta = :proxima_consulta,
                    status_tratamento = :status_tratamento,
                    updated_at = NOW()
                WHERE id = :id';

        $params = $this->hydrateParams($payload);
        $params['id'] = $id;

        $result = $this->pdo->prepare($sql)->execute($params);

        if ($result) {
            $this->refreshPatientSummary((int)$payload['user_id']);
        }

        return $result;
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }

        $stmt = $this->pdo->prepare('DELETE FROM tbl_meuapp_health WHERE id = :id');
        $result = $stmt->execute(['id' => $id]);

        if ($result) {
            $this->refreshPatientSummary((int)$record['user_id']);
        }

        return $result;
    }

    public function patientHasRecords(int $patientId): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM tbl_meuapp_health WHERE user_id = :uid LIMIT 1');
        $stmt->execute(['uid' => $patientId]);
        return $stmt->fetch() !== false;
    }

    private function hydrateParams(array $payload): array
    {
        return [
            'user_id' => (int)($payload['user_id'] ?? 0),
            'consulta_data' => $this->normalizeDateTime($payload['consulta_data'] ?? null),
            'estado' => $this->normalizeState($payload['estado'] ?? 'leve'),
            'diagnostico' => trim($payload['diagnostico'] ?? ''),
            'cid' => $this->normalizeCid($payload['cid'] ?? null),
            'tratamento' => $this->normalizeText($payload['tratamento'] ?? null),
            'prescricoes' => $this->normalizeText($payload['prescricoes'] ?? null),
            'observacoes' => $this->normalizeText($payload['observacoes'] ?? null),
            'medico_responsavel' => $this->normalizeText($payload['medico_responsavel'] ?? null),
            'instituicao' => $this->normalizeText($payload['instituicao'] ?? null),
            'pressao_arterial' => $this->normalizeText($payload['pressao_arterial'] ?? null),
            'frequencia_cardiaca' => $this->normalizeInteger($payload['frequencia_cardiaca'] ?? null),
            'temperatura_c' => $this->normalizeDecimal($payload['temperatura_c'] ?? null),
            'peso_kg' => $this->normalizeDecimal($payload['peso_kg'] ?? null),
            'altura_m' => $this->normalizeDecimal($payload['altura_m'] ?? null),
            'proxima_consulta' => $this->normalizeDateTime($payload['proxima_consulta'] ?? null),
            'status_tratamento' => $this->normalizeTreatmentStatus($payload['status_tratamento'] ?? 'em_andamento'),
        ];
    }

    private function normalizeDateTime(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = str_replace('T', ' ', trim($value));
        $timestamp = strtotime($value);

        return $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
    }

    private function normalizeState(string $state): string
    {
        $state = strtolower(trim($state));
        $valid = [
            'leve',
            'inspira_cuidados',
            'grave',
            'critico',
            'terminal',
            'recuperado',
            'estavel',
        ];

        return in_array($state, $valid, true) ? $state : 'leve';
    }

    private function normalizeTreatmentStatus(string $status): string
    {
        $status = strtolower(trim($status));
        $valid = ['em_andamento', 'concluido', 'suspenso'];
        return in_array($status, $valid, true) ? $status : 'em_andamento';
    }

    private function normalizeCid(?string $cid): ?string
    {
        if ($cid === null) {
            return null;
        }

        $formatted = strtoupper(trim($cid));
        return $formatted !== '' ? $formatted : null;
    }

    private function normalizeText(?string $text): ?string
    {
        if ($text === null) {
            return null;
        }

        $trimmed = trim($text);
        return $trimmed === '' ? null : $trimmed;
    }

    private function normalizeInteger($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        return $filtered !== false ? $filtered : null;
    }

    private function normalizeDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = str_replace(',', '.', (string)$value);
        return is_numeric($value) ? (float)$value : null;
    }

    private function refreshPatientSummary(int $patientId): void
    {
        $sql = 'SELECT consulta_data, estado, cid, instituicao
                  FROM tbl_meuapp_health
                 WHERE user_id = :uid
                 ORDER BY consulta_data DESC, id DESC
                 LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['uid' => $patientId]);
        $latest = $stmt->fetch();

        $update = $this->pdo->prepare(
            'UPDATE tbl_meuapp_patients SET
                last_consulta_em = :consulta,
                last_health_state = :estado,
                last_cid = :cid,
                ultima_clinica = :clinica,
                updated_at = updated_at
             WHERE id = :uid'
        );

        $update->execute([
            'consulta' => $latest['consulta_data'] ?? null,
            'estado' => $latest['estado'] ?? null,
            'cid' => $latest['cid'] ?? null,
            'clinica' => $latest['instituicao'] ?? null,
            'uid' => $patientId,
        ]);
    }
}
