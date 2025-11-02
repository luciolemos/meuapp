<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class BootstrapController extends BaseController
{
    public function index()
    {
        $this->renderView('documentacao/bootstrap.twig', [
            'title' => 'Bootstrap Framework',
            'subtitle'    => 'Configurando PHP, Apache e Twig',    
            'description' => 'Guia para configurar o ambiente (LAMP) Linux, Apache, MySQL e PHP, com Bootstrap e Twig',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-12',
            'export' => false
        ]);
    }
}
