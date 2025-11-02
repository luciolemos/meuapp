<?php
namespace App\Core;

use Throwable;

class Router
{
    /**
     * Resolve e executa a rota com base na URL fornecida.
     */
    public static function route(string $url): void
    {
        $url = trim($url ?? '', '/');
        if ($url === '') $url = 'home/index';

        $parts = explode('/', $url);
        $first = strtolower($parts[0] ?? '');
        $area = $first === 'admin' ? 'Admin' : 'Frontend';

        // Remove “admin” da rota se for o caso
        if ($area === 'Admin') {
            array_shift($parts);
        }

        $controller = 'Home';
        $method = 'index';
        $subDir = '';
        $params = [];

        try {
            // ======================================================
            // 🔹 ROTA ESPECIAL: /pdf/{tipo}
            // ======================================================
            if ($first === 'pdf') {
                $controller = 'Pdf';
                $method = 'gerar';
                $area = 'Frontend';
                $params = [$parts[1] ?? 'configuracao'];
            }

            // ======================================================
            // 🔹 ROTA CURTA: /erro/{codigo}
            // ======================================================
            elseif ($first === 'erro' && isset($parts[1])) {
                $controller = 'Erros';
                $method = 'mostrar';
                $params = [$parts[1]];
            }

            // ======================================================
            // 🔹 ROTA COMPLETA: /erros/mostrar/{codigo}
            // ======================================================
            elseif ($first === 'erros' && isset($parts[1]) && $parts[1] === 'mostrar') {
                $controller = 'Erros';
                $method = 'mostrar';
                $params = [$parts[2] ?? '404'];
            }

            // ======================================================
            // 🔹 ROTA PADRÃO (Ex: /documentacao/mvc)
            // ======================================================
            else {
                $originalParts = $parts;
                if (count($parts) === 1) {
                    $controller = self::slugToCamelCase($parts[0]);
                } elseif (count($parts) > 1) {
                    $method = array_pop($parts);
                    $controller = self::slugToCamelCase(array_pop($parts));

                    if (!empty($parts)) {
                        $subDir = '\\' . implode('\\', array_map('ucfirst', $parts));
                    }
                }
            }

            // ======================================================
            // 🔹 Monta o namespace final do controller
            // ======================================================
            $controllerClass = "\\App\\Http\\Controllers\\{$area}{$subDir}\\{$controller}Controller";

            // ======================================================
            // 🔹 Fallback: Detecta controllers em subdiretórios
            //              para rotas de dois segmentos (ex: /documentacao/conceito)
            // ======================================================
            if (
                (!class_exists($controllerClass) || !method_exists($controllerClass, $method))
                && isset($originalParts)
                && count($originalParts) === 2
            ) {
                [$maybeFolder, $maybeController] = $originalParts;
                $folderNamespace = '\\' . self::slugToCamelCase($maybeFolder);
                $altController = self::slugToCamelCase($maybeController);
                $altClass = "\\App\\Http\\Controllers\\{$area}{$folderNamespace}\\{$altController}Controller";

                if (class_exists($altClass)) {
                    $subDir = $folderNamespace;
                    $controller = $altController;
                    $method = method_exists($altClass, $method) ? $method : 'index';
                    $params = [];
                    $controllerClass = $altClass;
                }
            }

            // ======================================================
            // 🔹 Validação de existência do controlador
            // ======================================================
            if (!class_exists($controllerClass)) {
                $msg = "Controller {$controllerClass} não encontrado.";
                App::logError($msg);
                self::renderError(404, $msg);
            }

            $instance = new $controllerClass();

            // ======================================================
            // 🔹 Validação de existência do método
            // ======================================================
            if (!method_exists($instance, $method)) {
                $msg = "Método '{$method}' não encontrado em {$controllerClass}.";
                App::logError($msg);
                self::renderError(404, $msg);
            }

            // ======================================================
            // 🔹 Execução da ação
            // ======================================================
            $instance->$method(...$params);

        } catch (Throwable $e) {
            // ======================================================
            // 🔹 Captura de erros inesperados
            // ======================================================
            $msg = "Erro ao processar a rota '{$url}': " . $e->getMessage();
            App::logError($msg, $e);
            self::renderError(500, $msg);
        }
    }

    /**
     * Converte slug (ex: "meu-controller") em CamelCase ("MeuController")
     */
    private static function slugToCamelCase(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', strtolower($string))));
    }

    /**
     * Renderiza páginas de erro (404, 500 etc)
     */
    public static function renderError(int $code, string $message = ''): void
    {
         http_response_code($code);
        $view = match ($code) {
            403 => 'frontend/pages/erros/403.twig',
            404 => 'frontend/pages/erros/404.twig',
            default => 'frontend/pages/erros/500.twig',
        };
        try {
            $controller = new Controller();
            $controller->view($view, [
                'code' => $code,
                'message' => $message
            ]);
        } catch (Throwable $e) {
            // Fallback simples se o Twig falhar
            App::logError("Falha ao renderizar view de erro: " . $e->getMessage(), $e);
            echo "<h1>Erro {$code}</h1><p>{$message}</p>";
        }

        exit;
    }
}
