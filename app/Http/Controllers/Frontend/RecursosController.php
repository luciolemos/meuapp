<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

class RecursosController extends BaseController
{
    public function index(): void
    {
        $this->renderView('pages/recursos.twig', [
            'title'       => 'Recursos',
            'subtitle'    => 'Explore os principais recursos e funcionalidades oferecidos pelo MeuApp MVC.',
            'description' => 'Guia de configuração do ambiente PHP, Apache e Twig para rodar o MeuApp MVC localmente.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-24',
            'export' => false
        ]);
    }
}
