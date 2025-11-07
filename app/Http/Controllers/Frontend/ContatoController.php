<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

class ContatoController extends BaseController
{
    public function index(): void
    {
        $this->renderView('pages/contato.twig', [
            'title' => 'Contato',
            'subtitle' => 'Entre em contato com o Desenvolvedor do Projeto',
            'description' => 'Formulário de contato para dúvidas, sugestões ou suporte relacionado ao MeuApp MVC.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2025-10-16',
            'meta' => [
                'description' => 'Entre em contato com a equipe do MeuApp MVC para suporte, dúvidas ou parcerias.',
                'keywords'    => 'contato, suporte, meuapp mvc',
                'robots'      => 'index, follow',
            ],
            'layout' => [
                'hero' => true,
            ],
        ]);
    }

    public function enviar(): void
    {
        // Exemplo de lógica simulada de envio
        $this->renderView('contato.twig', [
            'title' => 'Mensagem enviada!',
            'subtitle' => 'Agradecemos seu contato, retornaremos em breve.'
        ]);
    }
}
