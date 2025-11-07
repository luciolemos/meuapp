<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Services\DatabasePatientRepository;
use App\Support\PatientOptions;
use App\Support\HealthOptions;

class PacientesController extends BaseController
{
    private DatabasePatientRepository $patients;
    private ?string $avatarUploadError = null;

    public function __construct()
    {
        parent::__construct();
        $this->patients = new DatabasePatientRepository();
    }

    public function index(): void
    {
        $this->renderView('patients.twig', [
            'title' => 'Gerenciamento de Pacientes',
            'patients' => $this->patients->all(),
            'status' => $_GET['status'] ?? null,
            'message' => $_GET['message'] ?? null,
            'convenioOptions' => PatientOptions::CONVENIOS,
            'situacaoOptions' => PatientOptions::SITUACOES,
            'healthStates' => HealthOptions::stateLabels(),
            'healthTreatments' => HealthOptions::treatmentStatuses(),
        ]);
    }

    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode('Método inválido.'));
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $convenio = PatientOptions::normalizeConvenio($_POST['convenio'] ?? null);
        $situacaoCadastro = PatientOptions::normalizeSituacao($_POST['situacao_cadastro'] ?? null);
        $avatar = trim($_POST['avatar'] ?? '');
        $birthDate = trim($_POST['birth_date'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($name === '' || $email === '') {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode('Nome e e-mail são obrigatórios.'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode('Informe um e-mail válido.'));
        }

        if ($this->patients->emailExists($email)) {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode('Já existe um paciente com este e-mail.'));
        }

        $avatarPath = $avatar !== '' ? $avatar : null;
        $uploadedAvatar = $this->uploadAvatar('avatar_file');
        if ($this->avatarUploadError) {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode($this->avatarUploadError));
        }
        if ($uploadedAvatar !== null) {
            $avatarPath = $uploadedAvatar;
        }

        $this->patients->create([
            'staff_id' => null,
            'name' => $name,
            'email' => $email,
            'convenio' => $convenio,
            'situacao_cadastro' => $situacaoCadastro,
            'avatar' => $avatarPath,
            'birth_date' => $birthDate,
            'cpf' => $cpf,
            'address' => $address,
        ]);

        $this->redirect('/admin/pacientes?status=success&message=' . urlencode('Paciente adicionado com sucesso.'));
    }

    public function update(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode('Método inválido.'));
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode('Paciente inválido.'));
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $convenio = PatientOptions::normalizeConvenio($_POST['convenio'] ?? null);
        $situacaoCadastro = PatientOptions::normalizeSituacao($_POST['situacao_cadastro'] ?? null);
        $avatar = trim($_POST['avatar'] ?? '');
        $birthDate = trim($_POST['birth_date'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($name === '' || $email === '') {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode('Nome e e-mail são obrigatórios.'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode('Informe um e-mail válido.'));
        }

        if ($this->patients->emailExists($email, $id)) {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode('Já existe um paciente com este e-mail.'));
        }

        $existing = $this->patients->find($id);
        $currentAvatar = $existing['avatar'] ?? null;
        $avatarPath = $avatar !== '' ? $avatar : $currentAvatar;

        $uploadedAvatar = $this->uploadAvatar('avatar_file', $currentAvatar);
        if ($this->avatarUploadError) {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode($this->avatarUploadError));
        }
        if ($uploadedAvatar !== null) {
            $avatarPath = $uploadedAvatar;
        }

        if (!$this->patients->update($id, [
            'name' => $name,
            'email' => $email,
            'convenio' => $convenio,
            'situacao_cadastro' => $situacaoCadastro,
            'avatar' => $avatarPath,
            'birth_date' => $birthDate,
            'cpf' => $cpf,
            'address' => $address,
        ])) {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode('Não foi possível atualizar o paciente.'));
        }

        $this->redirect('/admin/pacientes?status=success&message=' . urlencode('Paciente atualizado com sucesso.'));
    }

    public function delete(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode('Método inválido.'));
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode('Paciente inválido.'));
        }

        if (!$this->patients->delete($id)) {
            $this->redirect('/admin/pacientes?status=error&message=' . urlencode('Não foi possível remover o paciente.'));
        }

        $this->redirect('/admin/pacientes?status=success&message=' . urlencode('Paciente removido com sucesso.'));
    }

    private function redirect(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }

    private function uploadAvatar(string $field, ?string $currentPath = null): ?string
    {
        $this->avatarUploadError = null;

        if (!isset($_FILES[$field]) || !is_array($_FILES[$field])) {
            return null;
        }

        $file = $_FILES[$field];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->avatarUploadError = 'Falha ao receber o arquivo enviado.';
            return null;
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            $this->avatarUploadError = 'O avatar deve ter no máximo 2MB.';
            return null;
        }

        $tmp = $file['tmp_name'] ?? null;
        if (!$tmp || !is_uploaded_file($tmp)) {
            $this->avatarUploadError = 'Arquivo de upload inválido.';
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $tmp) : null;
        if ($finfo) {
            finfo_close($finfo);
        }

        $extension = match ($mime) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            default => null,
        };

        if ($extension === null) {
            $this->avatarUploadError = 'Formato de imagem não suportado. Utilize JPG ou PNG.';
            return null;
        }

        $rootPath = dirname(__DIR__, 4);
        $uploadDir = $rootPath . '/public/uploads/avatars';

        if (!is_dir($uploadDir)) {
            if (!@mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                $this->avatarUploadError = 'Não foi possível criar o diretório de uploads.';
                return null;
            }
        }

        if (!is_writable($uploadDir)) {
            @chmod($uploadDir, 0775);
            if (!is_writable($uploadDir)) {
                $this->avatarUploadError = 'Diretório de uploads sem permissão de escrita.';
                return null;
            }
        }

        try {
            $randomName = bin2hex(random_bytes(8));
        } catch (\Throwable $e) {
            $randomName = substr(md5(uniqid('avatar_', true)), 0, 16);
        }

        $filename = sprintf('avatar_%s.%s', $randomName, $extension);
        $destination = $uploadDir . '/' . $filename;

        if (!@move_uploaded_file($tmp, $destination)) {
            $this->avatarUploadError = 'Não foi possível salvar o arquivo enviado.';
            return null;
        }

        $relativePath = '/uploads/avatars/' . $filename;

        if ($currentPath && $this->isLocalAvatar($currentPath)) {
            $oldPath = $rootPath . '/public' . $currentPath;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        return $relativePath;
    }

    private function isLocalAvatar(string $path): bool
    {
        return str_starts_with($path, '/uploads/avatars/');
    }
}
