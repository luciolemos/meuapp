<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class WhatsphpController extends BaseController
{
    public function index(): void
    {
        $this->renderView('documentacao/whatsphp.twig', [
            'title' => 'O que é PHP?',
            'subtitle' => 'Entenda a linguagem que impulsiona o MeuApp MVC',
            'description' => 'História, características e motivos para continuar apostando em PHP 8+ no lado do servidor.',
            'author' => 'Equipe MeuApp',
            'date' => '2025-10-30',
            'export' => false,
        ]);
    }
}
