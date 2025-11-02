<?php

namespace App\Http\Middleware;

use App\Core\Request;

class RequestPathMiddleware
{
    public function handle(Request $request, callable $next)
    {
        // Captura o caminho da URI atual (sem query string)
        $uri = strtok($request->getUri(), '?');
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Normaliza (garante prefixo "/")
        if ($path === '') {
            $path = '/';
        }

        // Define a variável global "current_path" acessível no Twig
        $GLOBALS['current_path'] = $path;

        // Continua o fluxo normal
        return $next($request);
    }
}
