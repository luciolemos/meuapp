<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

class SobreController extends BaseController
{
    public function index(): void
    {
        $this->renderView('pages/sobre.twig', [
            'title'       => 'Sobre',
            'subtitle'    => 'Saiba mais a respeito do projeto',
            'description' => 'Guia de configuração do ambiente PHP, Apache e Twig para rodar o MeuApp MVC localmente.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2024-06-15',
            'export' => false
        ]);
    }
}
