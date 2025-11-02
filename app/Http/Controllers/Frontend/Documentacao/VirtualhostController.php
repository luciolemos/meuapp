<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class VirtualhostController extends BaseController
{
    public function index(): void
    {
        $this->renderView('documentacao/virtualhost.twig', [
            'title' => 'O Virtualhost',
            'subtitle'    => 'Página inicial do projeto',
            'description' => 'Guia de configuração do ambiente PHP, Apache e Twig para rodar o MeuApp MVC localmente.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2024-06-15',
            'export' => false
        ]);
    }
}
