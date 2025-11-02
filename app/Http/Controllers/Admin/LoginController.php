<?php
namespace App\Http\Controllers\Admin;

use App\Core\Controller;
use App\Services\AuthService;

class LoginController extends Controller
{
    public function index()
    {
        $this->renderView('login.twig', [
            'title' => 'Login Administrativo',
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function autenticar()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (AuthService::login($username, $password)) {
            header('Location: /admin/dashboard');
            exit;
        }

        header('Location: /admin/login?error=Credenciais inválidas');
        exit;
    }

    public function logout()
    {
        AuthService::logout();
        header('Location: /admin/login');
        exit;
    }
}
