<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class EstruturaController extends BaseController
{
    public function index(): void
    {
        $this->renderView('documentacao/estrutura.twig', [
            'title' => 'Estrutura do Projeto',
            'subtitle'    => 'Página inicial do projeto',
            'description' => 'Visão detalhada da estrutura de diretórios e organização do MeuApp MVC.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-21',
            'export' => false
        ]);
    }
}
