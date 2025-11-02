<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->renderView('pages/home.twig', [
            'title'       => 'Bem vindo',
            'subtitle'    => 'Página inicial do projeto',
            'description' => 'Recursos e funcionalidades do MeuApp MVC.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2024-06-15',
            'export' => false
        ]);
    }
}
