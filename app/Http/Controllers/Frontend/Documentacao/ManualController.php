<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class ManualController extends BaseController
{
/*     public function index()
    {
        $data = [
            'title' => 'Manual do projeto',
            'message' => 'Conceitos Fundamentais do Projeto MVC.'
        ];

        $this->view('manual.twig', $data);
    } */
           public function index(): void
    {
        $this->renderView('documentacao/manual.twig', [
            'title' => 'Descrição do Projeto',
            'description' => 'Entenda a arquitetura e os componentes do MeuApp MVC.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2024-06-15',
            'export' => false
        ]);
    }
}