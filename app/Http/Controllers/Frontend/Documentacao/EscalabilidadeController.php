<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class EscalabilidadeController extends BaseController
{
    public function index(): void
    {
        $this->renderView('documentacao/escalabilidade.twig', [
            'title' => 'Escalabilidade',
            'subtitle'    => 'Configurando PHP, Apache e Twig',    
            'description' => 'Guia para configurar o ambiente PHP, Apache e Twig para o MeuApp MVC.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-20',
            'export' => false
        ]);
    }
}
