<?php
namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Throwable;

class Controller
{
    protected Environment $twig;
    
    public function __construct()
    {
        $basePath = realpath(dirname(__DIR__, 2) . '/resources/views');
        $loader = new FilesystemLoader([
            $basePath,
            $basePath . '/layouts',
            $basePath . '/partials',
            $basePath . '/frontend',
            $basePath . '/admin',
        ]);
        $this->twig = new Environment($loader, [
            'cache' => dirname(__DIR__, 2) . '/storage/cache/twig',
            'auto_reload' => true,
            'debug' => true,
        ]);
        // Globais acessíveis em todas as views
        $this->twig->addGlobal('app_name', 'MeuApp MVC');
        $this->twig->addGlobal('base_url', '/');
        $this->twig->addGlobal('year', date('Y'));
        $this->twig->addGlobal('current_path', parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
        // Utiliza a instância centralizada do Twig (View::environment)
        $this->twig = View::environment();
    }


    /**
     * Renderiza uma view diretamente via Twig.
     */
    public function view(string $template, array $data = []): void
    {
        try {
            echo $this->twig->render($template, $data);
        } catch (Throwable $e) {
            // fallback para o renderizador compartilhado
            View::render($template, $data);
        }
    }

    /**
     * Renderiza automaticamente com base no tipo de controller (Admin/Public).
     */
    protected function renderView(string $template, array $data = []): void
{
    $isAdmin = str_contains(static::class, 'App\\Http\\Controllers\\Admin');
    $prefix = $isAdmin ? 'admin/' : 'frontend/';
    $template = ltrim($template, '/');
    $fullTemplate = $prefix . $template;

    try {
        echo $this->twig->render($fullTemplate, $data);
    } catch (Throwable $e) {
        View::render('frontend/pages/erros/500.twig', [
            'message' => 'Erro ao renderizar a view: ' . $fullTemplate,
            'details' => $e->getMessage(),
        ]);
    }
}

}
