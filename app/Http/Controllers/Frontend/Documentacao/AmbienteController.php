<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class AmbienteController extends BaseController
{
    public function index(): void
    {
        $this->renderView('documentacao/ambiente.twig', [
            'title'       => 'Ambiente de Desenvolvimento',
            'subtitle'    => 'Configurando Apache, PHP, Virtualhost, Composer, Twig e Bootstrap',               
            'description' => 'Guia de configuração do ambiente PHP, Apache e Twig para rodar o MeuApp MVC localmente.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-10',
            'export' => false
        ]);
    }
}
