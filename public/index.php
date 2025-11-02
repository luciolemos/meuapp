<?php
/**
 * ============================================================
 *  Ponto de entrada da aplicação MeuApp MVC
 *  ------------------------------------------------------------
 *  - Configura ambiente de execução e exibição de erros
 *  - Carrega o autoloader do Composer
 *  - Registra manipuladores globais de exceções e erros
 *  - Inicializa o núcleo da aplicação (App Core)
 * ============================================================
 */

declare(strict_types=1);

use App\Bootstrap\App as BootstrapApp;
use App\Core\App as CoreApp;

// ================================================
// 🔹 Configurações de exibição de erros (modo DEV)
// ================================================
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// ================================================
// 🔹 Autoload do Composer
// ================================================
require_once dirname(__DIR__) . '/vendor/autoload.php';

// ================================================
// 🔹 Manipulador global de exceções não tratadas
// ================================================
set_exception_handler(function (Throwable $e): void {
    // Registra no log de aplicação
    CoreApp::logError("Exceção não tratada: " . $e->getMessage(), $e);

    // Retorna resposta HTTP 500
    http_response_code(500);

    // Exibe página de erro amigável
    $errorView = dirname(__DIR__) . '/resources/views/errors/500.php';
    if (file_exists($errorView)) {
        include $errorView;
    } else {
        // Fallback simples se a view não existir
        echo "<h1>Erro Interno do Servidor (500)</h1>";
        echo "<p>Ocorreu um erro inesperado. Nossa equipe técnica foi notificada.</p>";
    }

    exit;
});

// ================================================
// 🔹 Manipulador global de erros PHP
// (converte avisos e notices em exceções tratáveis)
// ================================================
set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        // O erro foi silenciado com @
        return false;
    }

    // Converte o erro em uma exceção
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

// ================================================
// 🔹 Inicializa a aplicação (App → Router → Controller)
// ================================================
try {
    $app = BootstrapApp::boot();
    $app->run();
} catch (Throwable $e) {
    // Captura qualquer falha no bootstrap da aplicação
    CoreApp::logError("Erro fatal na inicialização da aplicação: " . $e->getMessage(), $e);
    http_response_code(500);

    echo "<h1>Falha crítica na inicialização</h1>";
    echo "<pre>{$e->getMessage()}</pre>";
    exit;
}
