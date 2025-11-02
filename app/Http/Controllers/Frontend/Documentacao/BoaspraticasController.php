<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class BoaspraticasController extends BaseController
{
    public function index()
    {
        $this->renderView('documentacao/boaspraticas.twig', [
            'title' => 'Boas Práticas de Desenvolvimento',
            'subtitle'    => 'Princípios e padrões para aplicações web em PHP',
            'description' => 'Construindo software de qualidade desde o primeiro commit',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-11',
            'export' => false
        ]);
    }
}
