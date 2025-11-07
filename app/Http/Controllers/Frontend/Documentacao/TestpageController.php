<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class TestpageController extends BaseController
{
    public function index(): void
    {
        $this->renderView('documentacao/testpage.twig', [
            'title'       => 'Página de testes',
            'subtitle'    => 'Veja aqui como sua view com Twig é renderezida',
            'description' => 'Guia de configuração do ambiente PHP, Apache e Twig para rodar o MeuApp MVC localmente.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2024-06-15',
            'export'      => false,
            'meta'        => [
                'description' => 'Página de testes para desenvolvimento com MeuApp MVC e Twig.',
                'keywords'    => 'teste, desenvolvimento, meuapp mvc',
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
