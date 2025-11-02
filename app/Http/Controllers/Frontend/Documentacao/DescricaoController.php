<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class DescricaoController extends BaseController
{
/*     public function index()
    {
        $data = [
            'title' => 'Descrição do Projeto',
            'message' => 'Entenda a arquitetura e os componentes do MeuApp MVC.'
        ];

        $this->view('descricao_projeto.twig', $data);
    } */
        public function index(): void
    {
        $this->renderView('documentacao/descricao.twig', [
            'title' => 'Descrição do Projeto',
            'subtitle'    => 'Configurando PHP, Apache e Twig',    
            'description' => 'Entenda a arquitetura e os componentes do MeuApp MVC.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-17',
            'export' => false
        ]);
    }
}
