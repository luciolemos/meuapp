<?php
namespace App\Services;

class AuthService
{
    public static function check(): bool
    {
        self::init();
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    public static function login(string $username, string $password): bool
    {
        self::init();

        // Simulação de login — em produção, isso viria do banco
        if ($username === 'admin' && $password === '123456') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $username;
            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        self::init();
        session_destroy();
    }

    private static function init(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
