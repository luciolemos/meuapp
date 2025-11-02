<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class HtaccessController extends BaseController
{
    public function index(): void
    {
        $this->renderView('documentacao/htaccess.twig', [
            'title' => 'Estrutura do .htaccess do Projeto',
            'subtitle'    => 'Página inicial do projeto',
            'description' => 'Guia para configurar o ambiente PHP, Apache e Twig para o MeuApp MVC.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-22',
            'export' => false
        ]);
    }
}
