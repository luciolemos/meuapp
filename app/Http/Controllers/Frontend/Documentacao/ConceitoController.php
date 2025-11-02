<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class ConceitoController extends BaseController
{
    public function index()
    {
        $this->renderView('documentacao/conceito.twig', [
            'title' => 'Conceito do projeto',
            'subtitle'    => 'Configurando PHP, Apache e Twig',            
            'description' => 'Guia para configurar o ambiente PHP, Apache e Twig para o MeuApp MVC.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-14',
            'export' => false
        ]);       
    }
}
