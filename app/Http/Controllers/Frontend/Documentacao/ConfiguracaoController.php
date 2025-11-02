<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class ConfiguracaoController extends BaseController
{
/*     public function index() 
    {
        $data = ['title' => 'Ambiente de Configuração'];
        $this->view('artigo_configuracao.twig', $data);
    } */

     public function index(): void
    {
        $this->renderView('documentacao/configuracao.twig', [
            'title' => 'Configuração do projeto',
            'subtitle' => 'Entre em contato com a equipe do MeuApp MVC.',
            'description' => 'Guia para configurar o ambiente PHP, Apache e Twig para o MeuApp MVC.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-15',
            'export' => false
        ]);       
    }
}
