<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class LinksController extends BaseController
{
    public function index()
    {
        $this->renderView('documentacao/links.twig', [
            'title'       => 'Links úteis',
            'subtitle'    => 'Sites oficiais das tecnologias do MeuApp MVC',    
            'description' => 'Coleção de referências seguras para documentação, downloads e guias das ferramentas utilizadas no projeto',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-18',
            'export' => false
        ]);
    }
}
