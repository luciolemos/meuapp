<?php
namespace App\Http\Controllers\Frontend\Documentacao;

use App\Http\Controllers\BaseController;

class GitgithubController extends BaseController
{
    public function index(): void
    {
        $this->renderView('documentacao/gitgithub.twig', [
            'title'       => 'Git & GitHub no MeuApp MVC',
            'subtitle'    => 'Fluxo de versionamento colaborativo, automações e boas práticas',
            'description' => 'Padronize branches, commits, revisões e pipelines para manter o projeto saudável e auditável.',
            'author'      => 'Equipe MeuApp',
            'date'        => '2024-06-15',
            'export'      => false,
        ]);
    }
}
