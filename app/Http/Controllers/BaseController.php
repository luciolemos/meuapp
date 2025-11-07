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

        $payload = $this->normalizePageData($data, $globals);
        $globals['export'] = $payload['export'];

        // ================================
        // 🧩 Mescla dados e renderiza
        // ================================
        $this->view($viewPath, array_merge($globals, $payload));
    }

    /**
     * Retorna os valores padrão de uma página pública/admin.
     */
    protected function pageDefaults(array $globals): array
    {
        return [
            'title' => $globals['app_name'],
            'subtitle' => '',
            'description' => '',
            'author' => '',
            'date' => '',
            'meta' => [
                'title' => null,
                'subtitle' => null,
                'description' => null,
                'keywords' => '',
                'robots' => 'index, follow',
            ],
            'layout' => [
                'hero' => true,
                'feature_cards' => false,
                'metrics' => false,
                'cta_banner' => false,
                'faq' => false,
                'newsletter' => false,
            ],
            'hero_actions' => [],
            'export' => false,
        ];
    }

    /**
     * Normaliza os dados recebidos dos controllers para garantir contrato consistente.
     */
    protected function normalizePageData(array $data, array $globals): array
    {
        $defaults = $this->pageDefaults($globals);
        $payload = array_replace_recursive($defaults, $data);

        if (empty($payload['title'])) {
            $payload['title'] = $globals['app_name'];
        }

        if (!isset($payload['layout']) || !is_array($payload['layout'])) {
            $payload['layout'] = $defaults['layout'];
        } else {
            $payload['layout'] = array_replace_recursive($defaults['layout'], $payload['layout']);
        }

        if (empty($payload['meta']['title'])) {
            $payload['meta']['title'] = $payload['title'];
        }

        if (empty($payload['meta']['description'])) {
            $payload['meta']['description'] = $payload['description'];
        }

        if (!isset($payload['meta']['keywords'])) {
            $payload['meta']['keywords'] = '';
        }

        if (!isset($payload['meta']['robots']) || $payload['meta']['robots'] === '') {
            $payload['meta']['robots'] = $defaults['meta']['robots'];
        }

        if (!isset($payload['hero_actions']) || !is_array($payload['hero_actions'])) {
            $payload['hero_actions'] = [];
        } else {
            $payload['hero_actions'] = array_values($payload['hero_actions']);
        }

        // Mantém compatibilidade com variáveis existentes e fornece estrutura "page"
        $payload['page'] = [
            'title' => $payload['title'],
            'subtitle' => $payload['subtitle'],
            'description' => $payload['description'],
            'author' => $payload['author'],
            'date' => $payload['date'],
            'meta' => $payload['meta'],
            'layout' => $payload['layout'],
            'actions' => $payload['hero_actions'],
        ];

        return $payload;
    }
}
