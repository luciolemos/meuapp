<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class StatusController extends BaseController
{
    public function index(): void
    {
        $this->renderView('documentacao/status.twig', [
            'title'       => 'Página vazia do Status',
            'subtitle'    => 'Página de status do projeto',
            'description' => 'Guia de configuração do ambiente PHP, Apache e Twig para rodar o MeuApp MVC localmente.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-26',
            'export' => false
        ]);
    }
}
