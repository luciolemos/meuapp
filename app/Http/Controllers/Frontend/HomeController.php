<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->renderView('pages/home.twig', [
            'title'       => 'Bem-vindo',
            'subtitle'    => 'Página inicial do projeto',
            'description' => 'Recursos e funcionalidades do MeuApp MVC.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2024-06-15',
            'meta' => [
                'description' => 'Conheça as principais funcionalidades, stack e diferenciais do MeuApp MVC logo na página inicial.',
                'keywords'    => 'home, meuapp mvc, php, twig, bootstrap',
            ],
            'layout' => [
                'feature_cards' => true,
                'metrics'       => true,
                'cta_banner'    => true,
                'faq'           => false,
            ],
            'hero_actions' => [
                [
                    'label'   => 'Conceito do projeto',
                    'href'    => '/documentacao/conceito',
                    'icon'    => 'fas fa-home me-2',
                    'variant' => 'warning',
                ],
            ],
        ]);
    }
}
