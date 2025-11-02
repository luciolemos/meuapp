<?php
namespace App\Core;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class View
{
    private static ?Environment $twig = null;

    /**
     * Inicializa o ambiente Twig compartilhado.
     */
    private static function init(): void
    {
        if (self::$twig instanceof Environment) {
            return;
        }

        $basePath = dirname(__DIR__, 2);
        $twigConfig = require $basePath . '/config/twig.php';
        $appConfig = require $basePath . '/config/app.php';

        $paths = $twigConfig['paths'] ?? [$basePath . '/resources/views'];
        $options = $twigConfig['options'] ?? [];
        $defaults = [
            'cache' => $basePath . '/storage/cache/twig',
            'auto_reload' => true,
            'debug' => false,
        ];
        $options = array_replace($defaults, $options);

        if (!empty($options['cache'])) {
            $cacheDir = $options['cache'];
            if (!is_dir($cacheDir)) {
                @mkdir($cacheDir, 0775, true);
            }
            if (!is_writable($cacheDir)) {
                $fallbackCache = $basePath . '/storage/cache/twig-runtime';
                if (!is_dir($fallbackCache)) {
                    mkdir($fallbackCache, 0775, true);
                }
                if (is_writable($fallbackCache)) {
                    $options['cache'] = $fallbackCache;
                } else {
                    $options['cache'] = false;
                }
            }
        }

        $loader = new FilesystemLoader($paths);
        self::$twig = new Environment($loader, $options);

        $appName = $appConfig['name'] ?? 'MeuApp MVC';
        $baseUrl = $appConfig['base_url'] ?? '/';
        $env = $_SERVER['APP_ENV'] ?? ($appConfig['app_env'] ?? 'dev');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $currentPath = parse_url($uri, PHP_URL_PATH) ?: '/';

        self::$twig->addGlobal('app_name', $appName);
        self::$twig->addGlobal('base_url', $baseUrl);
        self::$twig->addGlobal('year', date('Y'));
        self::$twig->addGlobal('current_path', $currentPath);
        self::$twig->addGlobal('app_env', $env);
    }

    /**
     * Retorna o ambiente Twig inicializado.
     */
    public static function environment(): Environment
    {
        self::init();
        return self::$twig;
    }

    /**
     * Renderiza uma view com tratamento de erros.
     */
    public static function render(string $template, array $data = []): void
    {
        self::init();

        try {
            echo self::$twig->render($template, $data);
        } catch (LoaderError | SyntaxError | RuntimeError $e) {
            self::handleViewError("Erro ao renderizar a view '{$template}'", $e);
        }
    }

    /**
     * Renderiza uma view assumindo o prefixo Admin ou Public.
     */
    public static function autoRender(string $template, array $data = [], bool $isAdmin = false): void
    {
        $prefix = $isAdmin ? 'admin/' : 'frontend/';
        $cleanTemplate = ltrim($template, '/');
        self::render($prefix . $cleanTemplate, $data);
    }

    /**
     * Fallback amigável para erros de renderização.
     */
    private static function handleViewError(string $message, \Throwable $exception): void
    {
        http_response_code(500);

        if (self::$twig instanceof Environment) {
            try {
                echo self::$twig->render('frontend/pages/erros/500.twig', [
                    'message' => $message,
                    'details' => $exception->getMessage(),
                ]);
                exit;
            } catch (\Throwable $fallback) {
                // Continua para o fallback simples em HTML.
            }
        }

        echo "<h1>Erro 500 - Problema ao renderizar view</h1>";
        echo "<p>{$message}</p>";
        echo "<pre>{$exception->getMessage()}</pre>";
        exit;
    }
}
