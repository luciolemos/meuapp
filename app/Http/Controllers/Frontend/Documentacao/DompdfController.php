<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class DompdfController extends BaseController
{
    public function index()
    {
        $this->renderView('documentacao/dompdf.twig', [
            'title' => 'DOMPDF com PHP',
            'subtitle'    => 'Configurando PHP, Apache e Twig',    
            'description' => 'Guia para configurar o ambiente (LAMP) Linux, Apache, MySQL e PHP, com DOMPDF, Bootstrap e Twig',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-19',
            'export' => false
        ]);
    }
}
