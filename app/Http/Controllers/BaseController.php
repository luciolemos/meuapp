<?php
namespace App\Http\Controllers;

use App\Core\Controller;

class BaseController extends Controller
{
    /**
     * Renderiza uma view automaticamente conforme o namespace do controller.
     * Exemplo: App\Http\Controllers\Frontend\HomeController → resources/views/frontend/home.twig
     */
    protected function renderView(string $template, array $data = []): void
    {
        // Determina se o Controller pertence ao namespace Admin ou Frontend
        $reflection = new \ReflectionClass($this);
        $namespace = $reflection->getNamespaceName();
        $prefix = str_contains($namespace, 'Admin') ? 'admin' : 'frontend';

        // Caminho final da view
        $viewPath = "{$prefix}/{$template}";

        // ================================
        // 🔧 Carrega config global
        // ================================
        $configPath = __DIR__ . '/../../../config/config.php';
        $config = file_exists($configPath) ? require $configPath : [];

        // ================================
        // 🌍 Define variáveis globais
        // ================================
        $globals = [
            'app_name' => $config['app_name'] ?? 'MeuApp MVC',
            'app_env'  => $config['app_env'] ?? 'production',
            'app_debug' => $config['app_debug'] ?? false,
            'year' => date('Y'),
            'export' => $data['export'] ?? false,
        ];

        // ================================
        // 🧩 Mescla dados e renderiza
        // ================================
        $this->view($viewPath, array_merge($globals, $data));
    }
}
