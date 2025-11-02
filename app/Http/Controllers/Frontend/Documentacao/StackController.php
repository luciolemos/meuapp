<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class StackController extends BaseController
{
    public function index()
    {
        $this->renderView('documentacao/stack.twig', [
            'title'       => 'Tecnologias utilizadas',
            'subtitle'    => 'Página inicial do projeto',
            'description' => 'Guia de configuração do ambiente PHP, Apache e Twig para rodar o MeuApp MVC localmente.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-25',
            'export' => false
        ]);       
    }
}
