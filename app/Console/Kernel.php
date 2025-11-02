<?php
namespace App\Console;

use App\Http\Middleware\RequestPathMiddleware;

class Kernel
{
    public static function commands()
    {
        echo "Console commands running...\n";
    }

        protected array $middleware = [
        RequestPathMiddleware::class,
        // ... outros middlewares globais
    ];
}
