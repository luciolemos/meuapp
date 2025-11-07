<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

class BlogController extends BaseController
{
    public function index(): void
    {
        $mainArticle = [
            'title'        => 'Construindo aplicações PHP modulares com o MeuApp MVC',
            'slug'         => 'construindo-aplicacoes-php-modulares',
            'excerpt'      => 'Entenda como estruturar controladores, rotas e componentes compartilhados para entregar features com mais agilidade e segurança.',
            'cover'        => '/images/notebooks/note_1600_900_3.png',
            'author'       => 'Equipe MeuApp',
            'reading_time' => '8 min',
            'published_at' => '2025-11-04',
            'tags'         => ['Arquitetura', 'Boas práticas', 'MVC'],
            'url'          => '/documentacao/conceito',
        ];

        $recentArticles = [
            [
                'title'        => '5 padrões para organizar controllers no seu próximo projeto PHP',
                'slug'         => 'padroes-para-organizar-controllers',
                'excerpt'      => 'Como aplicar Single Action Controllers, injeção de dependências e estratégias de versionamento em projetos reais.',
                'cover'        => '/images/notebooks/note_1600_900_4.png',
                'author'       => 'Lucio Lemos',
                'reading_time' => '6 min',
                'published_at' => '2025-11-04',
                'tags'         => ['Controllers', 'Clean Code'],
                'url'          => '#artigos-recentes',
            ],
            [
                'title'        => 'Integração de Twig com Bootstrap 5: componentes reutilizáveis em minutos',
                'slug'         => 'twig-bootstrap-componentes',
                'excerpt'      => 'Aprenda a criar macros Twig para componentes responsivos e aceleradores de UI no MeuApp MVC.',
                'cover'        => '/images/notebooks/note_1600_900_2.png',
                'author'       => 'Equipe MeuApp',
                'reading_time' => '7 min',
                'published_at' => '2025-11-04',
                'tags'         => ['Twig', 'Frontend'],
                'url'          => '#artigos-recentes',
            ],
            [
                'title'        => 'Checklist de implantação: como levar o MeuApp MVC para produção',
                'slug'         => 'checklist-implantacao-meuapp',
                'excerpt'      => 'Configurações essenciais de ambiente, logs e empacotamento para publicar o projeto com segurança.',
                'cover'        => '/images/notebooks/note_1600_900_5.png',
                'author'       => 'Equipe MeuApp',
                'reading_time' => '5 min',
                'published_at' => '2025-11-04',
                'tags'         => ['DevOps', 'Deployment'],
                'url'          => '#artigos-recentes',
            ],
        ];

        $topics = [
            [
                'label' => 'Arquitetura & Padrões',
                'description' => 'Refinando camadas, isolando responsabilidades e preparando o projeto para crescer.',
                'icon' => 'fas fa-layer-group',
            ],
            [
                'label' => 'Ferramentas & Automação',
                'description' => 'Scripts, pacotes composer e automações que aceleram seu pipeline.',
                'icon' => 'fas fa-robot',
            ],
            [
                'label' => 'Boas Práticas Frontend',
                'description' => 'UI responsiva com Bootstrap, componentes Twig e suporte a tema escuro.',
                'icon' => 'fas fa-laptop-code',
            ],
            [
                'label' => 'Lançamento & Observabilidade',
                'description' => 'Monitoramento, logs estruturados e estratégias de rollback.',
                'icon' => 'fas fa-chart-line',
            ],
        ];

        $this->renderView('pages/blog.twig', [
            'title'          => 'Blog',
            'subtitle'       => 'Insights sobre desenvolvimento PHP moderno e a evolução do MeuApp MVC.',
            'description'    => 'Tutoriais, histórias de bastidores e guias práticos para quem quer dominar o ecossistema do MeuApp.',
            'author'         => $mainArticle['author'],
            'date'           => $mainArticle['published_at'],
            'meta' => [
                'description' => 'Fique por dentro das novidades do MeuApp MVC, guias técnicos e boas práticas para construir projetos escaláveis.',
                'keywords'    => 'blog, php, mvc, boas práticas, meuapp',
            ],
            'layout' => [
                'hero' => false,
            ],
            'mainArticle'    => $mainArticle,
            'recentArticles' => $recentArticles,
            'topics'         => $topics,
        ]);
    }
}
