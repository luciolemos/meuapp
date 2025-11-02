<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class WhatsapacheController extends BaseController
{
    public function index(): void
    {
        $this->renderView('documentacao/whatsapache.twig', [
            'title' => 'O que é o Apache?',
            'subtitle' => 'Entenda o servidor Apache no MeuApp MVC',
            'description' => 'História, características e motivos para continuar apostando em PHP 8+ no lado do servidor.',
            'author' => 'Equipe MeuApp',
            'date' => '2025-10-30',
            'export' => false,
        ]);
    }
}
