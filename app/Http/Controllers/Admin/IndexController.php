<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;

class IndexController extends BaseController
{
    public function index(): void
    {
        $this->renderView('dashboard.twig', [
            'title' => 'Painel Administrativo',
            'welcome' => 'Bem-vindo ao painel admin do MeuApp MVC!'
        ]);
    }
}
