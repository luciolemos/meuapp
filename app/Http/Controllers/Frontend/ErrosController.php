<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

class ErrosController extends BaseController
{
    public function index(): void
    {
        $this->renderView('erros.twig', [
            'title' => 'Demonstração das Páginas de Erro',
            'subtitle'    => 'Páginas de erro personalizadas',
            'description' => 'Visualize como o MeuApp MVC exibe e trata erros de forma elegante.',
            'keywords'    => 'erros, páginas de erro, personalização, tratamento de erros, MeuApp MVC',
            'robots'     => 'noindex, nofollow',
            'header' => true,
            'footer' => true,
            'sidebar' => false,
            'ads' => false,
            'comments' => false,
            'share' => false,
            'pagination' => false,
            'search' => false,
            'tags' => false,
            'author' => false,
            'date' => false,
            'views' => false,
            'likes' => false,
            'dislikes' => false,
            'related' => false,
            'comments_section' => false,
            'newsletter' => false,
            'social_media' => false,
            'contact_form' => false,
            'gallery' => false,
            'video' => false,
            'audio' => false,
            'map' => false,
            'faq' => false,
            'testimonials' => false,
            'team' => false,
            'pricing' => false,
            'features' => false,
            'blog' => false,
            'portfolio' => false,
            'services' => false,
            'products' => false,
            'categories' => false,
            'tags_list' => false,
            'search_bar' => false,
            'filter' => false,
            'sort' => false,
            'newsletter_signup' => false,       
            'contact_info' => false,
            'social_links' => false,
            'footer_links' => false,
            'export' => false,
        ]);
    }

    public function mostrar(string $codigo = '404'): void
    {
        $permitidos = ['403', '404', '500'];
        if (!in_array($codigo, $permitidos)) {
            $codigo = '404';
        }

        $this->renderView("pages/erros/{$codigo}.twig", [
            'title' => "Erro {$codigo}"
        ]);
    }
}
