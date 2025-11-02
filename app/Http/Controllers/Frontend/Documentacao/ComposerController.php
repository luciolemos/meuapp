<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class ComposerController extends BaseController
{
    public function index()
    {
        $this->renderView('documentacao/composer.twig', [
            'title'       => 'Composer',
            'subtitle'    => 'Gerenciador de dependências para PHP',
            'description' => 'Recursos e funcionalidades do MeuApp MVC.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-13',
            'export' => false
        ]);
    }
}
