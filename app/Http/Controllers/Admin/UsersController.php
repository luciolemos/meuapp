<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;

class UsersController extends BaseController
{
    public function index(): void
    {
        $this->renderView('admin_users.twig', [
            'title' => 'Gerenciamento de Usuários',
            'users' => [
                ['id' => 1, 'name' => 'João', 'email' => 'joao@meuapp.local'],
                ['id' => 2, 'name' => 'Maria', 'email' => 'maria@meuapp.local']
            ]
        ]);
    }
}
