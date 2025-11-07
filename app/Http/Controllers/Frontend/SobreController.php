<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

class SobreController extends BaseController
{
    public function index(): void
    {
        $this->renderView('pages/sobre.twig', [
            'title'       => 'Sobre',
            'subtitle'    => 'Saiba mais a respeito do projeto',
            'description' => 'Guia de configuração do ambiente PHP, Apache e Twig para rodar o MeuApp MVC localmente.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2024-06-15',
            'meta' => [
                'description' => 'História, objetivos e evolução do MeuApp MVC com foco em arquitetura MVC moderna.',
                'keywords'    => 'sobre, arquitetura mvc, meuapp',
            ],
            'layout' => [
                'hero' => true,
                'feature_cards' => true,
                'metrics'       => true,
                'cta_banner'    => true,
                'faq'           => true,
                'newsletter'    => false,
            ],
            'hero_actions' => [
                [
                    'label'   => 'Manual completo',
                    'href'    => '/documentacao/conceito',
                    'icon'    => 'fas fa-book',
                    'variant' => 'danger',
                ],
                [
                    'label'   => 'Estrutura do projeto',
                    'href'    => '/documentacao/estrutura',
                    'icon'    => 'fab fa-github',
                    'variant' => 'warning',
                ],
            ],
        ]);
    }
}
