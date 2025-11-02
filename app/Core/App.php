<?php
namespace App\Core;

use Throwable;

class App
{
    /**
     * Inicializa o núcleo da aplicação
     */
    public static function init(): void
    {
        self::bootstrap();
        self::run();
    }

    /**
     * Executa etapas de preparação (env, diretórios, providers)
     */
    public static function bootstrap(): void
    {
        self::registerEnvironment();
        self::createStorageFolders();
    }

    /**
     * Dispara o ciclo principal da aplicação (roteamento)
     */
    public static function run(): void
    {
        try {
            $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
            Router::route($uri);
        } catch (Throwable $e) {
            self::logError(
                'Erro fatal durante a execução da aplicação: ' . $e->getMessage(),
                $e
            );
            http_response_code(500);
            echo "<h1>Erro Interno do Servidor</h1>";
            echo "<pre>{$e->getMessage()}</pre>";
            exit;
        }
    }

    /**
     * Define constantes globais apenas uma vez
     */
    private static function registerEnvironment(): void
    {
        if (!defined('BASE_PATH'))     define('BASE_PATH', realpath(__DIR__ . '/../../'));
        if (!defined('APP_PATH'))      define('APP_PATH', BASE_PATH . '/app');
        if (!defined('CONFIG_PATH'))   define('CONFIG_PATH', BASE_PATH . '/config');
        if (!defined('VIEW_PATH'))     define('VIEW_PATH', BASE_PATH . '/resources/views');
        if (!defined('STORAGE_PATH'))  define('STORAGE_PATH', BASE_PATH . '/storage');

        // Ambiente de execução
        if (!defined('APP_ENV')) {
            define('APP_ENV', $_SERVER['APP_ENV'] ?? 'dev');
        }

        ini_set('display_errors', APP_ENV === 'dev' ? '1' : '0');
    }

    /**
     * Garante que diretórios essenciais existam
     */
    private static function createStorageFolders(): void
    {
        $folders = [
            STORAGE_PATH,
            STORAGE_PATH . '/logs',
            STORAGE_PATH . '/cache',
            STORAGE_PATH . '/cache/twig'
        ];

        foreach ($folders as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
        }
    }

    /**
     * Registra erros em /storage/logs/app.log
     */
     public static function logError(string $message, ?\Throwable $exception = null): void
    {
        try {
            $logDir = __DIR__ . '/../../storage/logs';
            $logFile = $logDir . '/app.log';

            if (!is_dir($logDir)) {
                mkdir($logDir, 0775, true);
            }

            $date = (new \DateTime())->format('Y-m-d H:i:s');

            $type = $exception ? get_class($exception) : 'Log';
            $trace = $exception ? " | {$exception->getFile()}:{$exception->getLine()}" : '';

            $formatted = "[{$date}] [{$type}] {$message}{$trace}\n";
            file_put_contents($logFile, $formatted, FILE_APPEND);
        } catch (\Throwable $e) {
            // Fallback: se der erro ao registrar, evita loop infinito
            error_log("Falha ao registrar log: " . $e->getMessage());
        }
    }
}
