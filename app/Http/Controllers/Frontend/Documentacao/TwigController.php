<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class TwigController extends BaseController
{
    public function index()
    {
        $this->renderView('documentacao/twig.twig', [
            'title' => 'Twig Template Engine',
            'subtitle'    => 'Página inicial do projeto',
            'description' => 'Guia de configuração do ambiente PHP, Apache e Twig para rodar o MeuApp MVC localmente.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2024-06-15',
            'export' => false
        ]);       
    }
}
