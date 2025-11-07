<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use DateTimeImmutable;

class SettingsController extends BaseController
{
    private string $settingsFile;

    public function __construct()
    {
        parent::__construct();
        $this->settingsFile = dirname(__DIR__, 4) . '/storage/data/settings.json';
    }

    public function index(): void
    {
        $this->renderView('settings.twig', [
            'title' => 'Configurações do Sistema',
            'settings' => $this->loadSettings(),
            'status' => $_GET['status'] ?? null,
            'message' => $_GET['message'] ?? null,
        ]);
    }

    public function update(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/admin/settings?status=error&message=' . urlencode('Método inválido.'));
        }

        $appName = trim($_POST['app_name'] ?? '');
        $appTagline = trim($_POST['app_tagline'] ?? '');
        $contactEmail = trim($_POST['contact_email'] ?? '');
        $supportPhone = trim($_POST['support_phone'] ?? '');
        $timezone = trim($_POST['timezone'] ?? '');
        $appEnv = trim($_POST['app_env'] ?? 'dev');
        $appDebug = isset($_POST['app_debug']) && $_POST['app_debug'] === '1';

        if ($appName === '') {
            $this->redirect('/admin/settings?status=error&message=' . urlencode('O nome da aplicação é obrigatório.'));
        }

        if ($contactEmail !== '' && !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/admin/settings?status=error&message=' . urlencode('Informe um e-mail válido.'));
        }

        $allowedEnv = ['dev', 'production', 'staging'];
        if (!in_array($appEnv, $allowedEnv, true)) {
            $appEnv = 'dev';
        }

        if ($timezone === '') {
            $timezone = 'America/Sao_Paulo';
        }

        $settings = [
            'app_name' => $appName,
            'app_tagline' => $appTagline,
            'contact_email' => $contactEmail,
            'support_phone' => $supportPhone,
            'timezone' => $timezone,
            'app_env' => $appEnv,
            'app_debug' => $appDebug,
            'updated_at' => (new DateTimeImmutable())->format(DATE_ATOM),
        ];

        if (!$this->persistSettings($settings)) {
            $this->redirect('/admin/settings?status=error&message=' . urlencode('Não foi possível salvar as configurações.'));
        }

        $this->redirect('/admin/settings?status=success&message=' . urlencode('Configurações atualizadas com sucesso.'));
    }

    private function loadSettings(): array
    {
        $config = require dirname(__DIR__, 4) . '/config/config.php';
        $defaults = [
            'app_name' => $config['app_name'] ?? 'MeuApp MVC',
            'app_tagline' => 'Painel administrativo do MeuApp',
            'contact_email' => 'contato@meuapp.local',
            'support_phone' => '',
            'timezone' => $config['timezone'] ?? 'America/Sao_Paulo',
            'app_env' => $config['app_env'] ?? 'dev',
            'app_debug' => (bool)($config['app_debug'] ?? false),
            'updated_at' => null,
        ];

        if (is_file($this->settingsFile) && is_readable($this->settingsFile)) {
            $payload = json_decode((string)file_get_contents($this->settingsFile), true);
            if (is_array($payload)) {
                $defaults = array_merge($defaults, $payload);
            }
        }

        return $defaults;
    }

    private function persistSettings(array $settings): bool
    {
        $directory = dirname($this->settingsFile);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            return false;
        }

        $json = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return false;
        }

        return file_put_contents($this->settingsFile, $json) !== false;
    }

    private function redirect(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}
