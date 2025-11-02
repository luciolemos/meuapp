<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

class TestandoController extends BaseController
{
    public function index(): void
    {
        $this->renderView('pages/testando.twig', [
            'title' => 'Página de teste',
            'subtitle'    => 'Página inicial do projeto',
            'description' => 'Guia de configuração do ambiente PHP, Apache e Twig para rodar o MeuApp MVC localmente.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-27',
            'export' => false
        ]);
    }
}
