<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

class ShowcaseController extends BaseController
{
    public function index(): void
    {
        $this->renderView('pages/showcase.twig', [
            'title'       => 'Showcase de Layout Dinâmico',
            'subtitle'    => 'Demonstração de uso de flags e conteúdo parametrizado',
            'description' => 'Exibe como habilitar ou desabilitar seções da página controlando chaves no controller.',
            'meta' => [
                 'title'       => 'Showcase de Layout Dinâmico',
                 'subtitle'    => 'Demonstração de uso de flags e conteúdo parametrizado',
                 'description' => 'Exibe como habilitar ou desabilitar seções da página controlando chaves no controller.',
                 'keywords'    => 'showcase, flags, layout, controller, twig',
                 'robots'      => 'noindex, nofollow',
            ],
            'layout' => [
                'feature_cards' => true,
                'metrics'       => true,
                'cta_banner'    => true,
                'faq'           => true,
                'newsletter'    => false,
            ],
            'hero_actions' => [
                [
                    'label'   => 'Manual completo',
                    'href'    => '/documentacao/manual',
                    'icon'    => 'fas fa-book',
                    'variant' => 'success',
                ],
                [
                    'label'   => 'Fluxo Git & GitHub',
                    'href'    => '/documentacao/gitgithub',
                    'icon'    => 'fab fa-github',
                    'variant' => 'primary',
                ],
            ],
            'features' => [
                [
                    'icon'  => 'fas fa-layer-group text-primary',
                    'title' => 'Layout controlado por flags',
                    'text'  => 'Ative ou desative blocos apenas ajustando valores booleanos no controller.',
                ],
                [
                    'icon'  => 'fas fa-code text-success',
                    'title' => 'Twig limpo',
                    'text'  => 'A view consome o mapa de opções e rende apenas o necessário.',
                ],
                [
                    'icon'  => 'fas fa-external-link-square-alt text-warning',
                    'title' => 'Extensível',
                    'text'  => 'Replique este padrão para outras páginas sem duplicar lógica.',
                ],
            ],
            'metrics' => [
                ['label' => 'Seções ativas', 'value' => 3],
                ['label' => 'Seções inativas', 'value' => 2],
                ['label' => 'Componentes reutilizados', 'value' => 5],
            ],
            'cta' => [
                'heading' => 'Gostou do padrão?',
                'text'    => 'Aproveite esses blocos para montar páginas moduláveis em qualquer parte do projeto.',
                'button'  => [
                    'label' => 'Ver documentação',
                    'url'   => '/documentacao/manual',
                ],
            ],
            'faq_items' => [
                [
                    'question' => 'Onde defino os padrões?',
                    'answer'   => 'No BaseController ou numa camada de config, usando array_replace_recursive para mesclar com os dados da página.',
                ],
                [
                    'question' => 'Posso reutilizar isto em outras views?',
                    'answer'   => 'Sim! Basta replicar o bloco condicional e passar flags diferentes no controller desejado.',
                ],
                [
                    'question' => 'Preciso alterar a view para cada combinação?',
                    'answer'   => 'Não. A view lê o mapa de flags e mantém a renderização consistente independentemente da combinação escolhida.',
                ],
            ],
        ]);
    }
}
