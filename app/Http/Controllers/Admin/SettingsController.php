<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;

class SettingsController extends BaseController
{
    public function index(): void
    {
        $this->renderView('admin_settings.twig', [
            'title' => 'Configurações do Sistema',
            'options' => [
                'timezone' => 'America/Sao_Paulo',
                'debug' => true
            ]
        ]);
    }
}
