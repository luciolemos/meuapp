<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class MvcController extends BaseController
{
    public function index(): void
    {
        $this->renderView('documentacao/mvc.twig', [
            'title'       => 'Entendendo o Padrão MVC',
            'subtitle'    => 'Separando responsabilidades entre Model, View e Controller',
            'description' => 'Como o padrão MVC organiza o código promovendo reutilização, manutenção simplificada e arquitetura limpa.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-23',
            'export' => false
        ]);
    }
}
