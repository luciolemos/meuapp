<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Services\DatabaseHealthRepository;
use App\Services\DatabasePatientRepository;
use App\Support\HealthOptions;
use Throwable;

class HealthController extends BaseController
{
    private DatabaseHealthRepository $health;
    private DatabasePatientRepository $patients;

    public function __construct()
    {
        parent::__construct();
        $this->health = new DatabaseHealthRepository();
        $this->patients = new DatabasePatientRepository();
    }

    public function index(): void
    {
        $this->renderView('health.twig', [
            'title' => 'Acompanhamento de Saúde',
            'records' => $this->health->all(),
            'patients' => $this->patients->all(),
            'states' => HealthOptions::states(),
            'treatment_statuses' => HealthOptions::treatmentStatuses(),
            'status' => $_GET['status'] ?? null,
            'message' => $_GET['message'] ?? null,
        ]);
    }

    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirectWithError('Método inválido.');
        }

        $payload = $this->collectPayload($_POST);
        $errors = $this->validatePayload($payload);

        if (!empty($errors)) {
            $this->redirect('/admin/health?status=error&message=' . urlencode(implode(' ', $errors)));
        }

        try {
            $this->health->create($payload);
        } catch (Throwable $e) {
            $this->redirect('/admin/health?status=error&message=' . urlencode('Não foi possível registrar o atendimento.'));
        }

        $this->redirect('/admin/health?status=success&message=' . urlencode('Atendimento registrado com sucesso.'));
    }

    public function update(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirectWithError('Método inválido.');
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            $this->redirectWithError('Registro inválido.');
        }

        $existing = $this->health->find($id);
        if (!$existing) {
            $this->redirectWithError('Registro não encontrado.');
        }

        $payload = $this->collectPayload($_POST);
        $errors = $this->validatePayload($payload);

        if (!empty($errors)) {
            $this->redirect('/admin/health?status=error&message=' . urlencode(implode(' ', $errors)));
        }

        try {
            $this->health->update($id, $payload);
        } catch (Throwable $e) {
            $this->redirect('/admin/health?status=error&message=' . urlencode('Falha ao atualizar o atendimento.'));
        }

        $this->redirect('/admin/health?status=success&message=' . urlencode('Atendimento atualizado com sucesso.'));
    }

    public function delete(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirectWithError('Método inválido.');
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            $this->redirectWithError('Registro inválido.');
        }

        if (!$this->health->delete($id)) {
            $this->redirect('/admin/health?status=error&message=' . urlencode('Não foi possível remover o atendimento.'));
        }

        $this->redirect('/admin/health?status=success&message=' . urlencode('Atendimento removido com sucesso.'));
    }

    public function history(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método inválido.']);
            return;
        }

        $patientId = isset($_GET['patient']) ? (int)$_GET['patient'] : 0;
        if ($patientId <= 0) {
            http_response_code(422);
            echo json_encode(['error' => 'Paciente inválido.']);
            return;
        }

        $records = $this->health->all($patientId);
        $states = HealthOptions::states();
        $statuses = HealthOptions::treatmentStatuses();

        $data = array_map(function (array $record) use ($states, $statuses): array {
            $consulta = $record['consulta_data'] ?? null;
            $estado = $record['estado'] ?? null;
            $status = $record['status_tratamento'] ?? null;

            return [
                'id' => (int)($record['id'] ?? 0),
                'consulta_data' => $consulta,
                'consulta_label' => $consulta ? date('d/m/Y H:i', strtotime($consulta)) : '—',
                'estado' => $estado,
                'estado_label' => $states[$estado]['label'] ?? ucfirst((string)$estado),
                'diagnostico' => $record['diagnostico'] ?? '',
                'cid' => $record['cid'] ?? '',
                'medico_responsavel' => $record['medico_responsavel'] ?? '',
                'instituicao' => $record['instituicao'] ?? '',
                'status_tratamento' => $status,
                'status_label' => $statuses[$status] ?? ucfirst((string)$status),
            ];
        }, $records);

        echo json_encode(['data' => $data]);
    }

    private function collectPayload(array $input): array
    {
        return [
            'user_id' => $input['user_id'] ?? null,
            'consulta_data' => $input['consulta_data'] ?? null,
            'estado' => $input['estado'] ?? HealthOptions::defaultState(),
            'diagnostico' => $input['diagnostico'] ?? '',
            'cid' => $input['cid'] ?? null,
            'tratamento' => $input['tratamento'] ?? null,
            'prescricoes' => $input['prescricoes'] ?? null,
            'observacoes' => $input['observacoes'] ?? null,
            'medico_responsavel' => $input['medico_responsavel'] ?? null,
            'instituicao' => $input['instituicao'] ?? null,
            'pressao_arterial' => $input['pressao_arterial'] ?? null,
            'frequencia_cardiaca' => $input['frequencia_cardiaca'] ?? null,
            'temperatura_c' => $input['temperatura_c'] ?? null,
            'peso_kg' => $input['peso_kg'] ?? null,
            'altura_m' => $input['altura_m'] ?? null,
            'proxima_consulta' => $input['proxima_consulta'] ?? null,
            'status_tratamento' => $input['status_tratamento'] ?? HealthOptions::defaultTreatmentStatus(),
        ];
    }

    private function validatePayload(array $payload): array
    {
        $errors = [];

        $userId = isset($payload['user_id']) ? (int)$payload['user_id'] : 0;
        if ($userId <= 0 || !$this->patients->find($userId)) {
            $errors[] = 'Selecione um paciente válido.';
        }

        $diagnostico = trim($payload['diagnostico'] ?? '');
        if ($diagnostico === '') {
            $errors[] = 'Informe o diagnóstico.';
        }

        $consulta = $payload['consulta_data'] ?? '';
        if (trim((string)$consulta) === '') {
            $errors[] = 'Informe a data da consulta.';
        }

        return $errors;
    }

    private function redirectWithError(string $message): void
    {
        $this->redirect('/admin/health?status=error&message=' . urlencode($message));
    }

    private function redirect(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}
