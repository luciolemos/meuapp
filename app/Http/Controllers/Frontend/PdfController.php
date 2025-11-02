<?php
namespace App\Http\Controllers\Frontend;

use App\Core\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use Throwable;

class PdfController extends Controller
{
    /**
     * Gera PDF modular com sumário clicável e índice lateral (bookmarks)
     */
    public function gerar(string $tipo = 'configuracao'): void
    {
        try {
            $tipo = strtolower(urldecode($tipo));
            set_time_limit(600);
            ini_set('memory_limit', '1536M');

            $root = realpath(__DIR__ . '/../../../');
            $title = $this->getTitulo($tipo);

            // Caminho temporário dentro da pasta pública (seguro)
            $tmpDir = "{$root}/public/tmp/pdf";
            if (!is_dir($tmpDir)) mkdir($tmpDir, 0777, true);
            $this->limparTemporarios($tmpDir);

            // Configurações DOMPDF
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isFontSubsettingEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->setChroot($root); // Libera acesso ao projeto inteiro
            $dompdf = new Dompdf($options);

            // CSS e logo
            $cssPath = "{$root}/public/css/pdf-style.css";
            $css = file_exists($cssPath) ? file_get_contents($cssPath) : '';
            $logoPath = "{$root}/public/images/logo.png";
            $logoBase64 = file_exists($logoPath)
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                : '';

            // Conteúdo HTML modularizado
            if ($tipo === 'configuracao') {
                $sections = [
                    'descricao', 'conceito', 'whatsphp', 'stack', 'estrutura', 'mvc',
                    'twig', 'bootstrap', 'htaccess', 'virtualhost', 'ambiente',
                    'composer', 'gitgithub', 'dompdf', 'diagnostico', 'escalabilidade', 'boaspraticas'
                ];

                $htmlGlobal  = $this->renderCapa($logoBase64);
                $htmlGlobal .= $this->renderSumario($sections);

                foreach ($sections as $slug) {
                    $html = $this->safeRender("frontend/documentacao/{$slug}.twig", [
                        'title' => ucfirst($slug),
                        'export' => true,
                        'anchor' => $slug
                    ]);
                    file_put_contents("{$tmpDir}/{$slug}.html", $html);
                }

                foreach ($sections as $slug) {
                    $htmlGlobal .= file_get_contents("{$tmpDir}/{$slug}.html");
                    $htmlGlobal .= '<div class="page-break"></div>';
                }
            } else {
                $htmlGlobal = $this->safeRender("frontend/documentacao/{$tipo}.twig", [
                    'title' => $title,
                    'export' => true
                ]);
            }

            // Montagem final do HTML
            $finalHtml = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <style>{$css}</style>
            </head>
            <body>
                {$this->renderHeader($logoBase64, $title)}
                <main>{$htmlGlobal}</main>
                {$this->renderFooter()}
            </body>
            </html>
            HTML;

            $tmpFile = "{$tmpDir}/final.html";
            file_put_contents($tmpFile, $finalHtml);

            // Renderiza com DOMPDF
            $dompdf->loadHtmlFile($tmpFile);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Adiciona bookmarks (índice lateral)
            $this->adicionarBookmarks($dompdf, $tipo);

            // Numeração e data
            $canvas = $dompdf->getCanvas();
            $font = $dompdf->getFontMetrics()->getFont("DejaVu Sans", "normal");
            $canvas->page_script('
                $font = $fontMetrics->getFont("DejaVu Sans", "normal");
                $size = 8;
                if ($PAGE_NUM > 1) {
                    $text = "Página $PAGE_NUM de $PAGE_COUNT";
                    $pdf->text(520, 820, $text, $font, $size);
                    $pdf->text(50, 820, "Gerado em " . date("d/m/Y H:i"), $font, $size);
                }
            ');

            ob_clean();
            $dompdf->stream("meuapp_{$tipo}.pdf", ["Attachment" => false]);
            exit;
        } catch (Throwable $e) {
            echo "<h2>Erro ao gerar PDF: {$e->getMessage()}</h2>";
            exit;
        }
    }

    // =========================================================
    // 🔹 Funções auxiliares
    // =========================================================

    private function getTitulo(string $tipo): string
    {
        return match ($tipo) {
            'configuracao'   => 'Configuração do Ambiente',
            'conceito'       => 'Conceito do Projeto',
            'estrutura'      => 'Estrutura do Projeto',
            'twig'           => 'Twig Template Engine',
            'bootstrap'      => 'Bootstrap no Projeto',
            'boaspraticas'   => 'Boas Práticas',
            'escalabilidade' => 'Escalabilidade',
            'htaccess'       => 'Arquivo .htaccess',
            'virtualhost'    => 'VirtualHost Apache',
            'composer'       => 'Composer e PSR-4',
            'gitgithub'      => 'Git & GitHub no MeuApp',
            'dompdf'         => 'Geração de PDFs com Dompdf',
            'diagnostico'    => 'Diagnóstico do Sistema',
            'ambiente'       => 'Ambiente de Desenvolvimento',
            'stack'          => 'Stack do Projeto',
            'mvc'            => 'Arquitetura MVC',
            'descricao'      => 'Descrição do Projeto',
            'recursos'       => 'Recursos do Projeto',
            default          => ucfirst($tipo),
        };
    }

    private function safeRender(string $view, array $data = []): string
    {
        try {
            return $this->twig->render($view, $data);
        } catch (Throwable $e) {
            return "<section style='color:#842029;background:#f8d7da;padding:10px;border-radius:6px;margin:10px 0;'>
                <h3>❌ Falha ao carregar {$view}</h3>
                <p>{$e->getMessage()}</p>
            </section>";
        }
    }

    private function renderCapa(string $logoBase64): string
    {
        return <<<HTML
        <section class="cover-page">
            <img src="{$logoBase64}" alt="Logo MeuApp">
            <h1>MeuApp MVC</h1>
            <h2>Documentação Técnica Completa</h2>
            <p>PHP • Twig • Composer • Bootstrap • Apache</p>
            <hr>
            <p class="credits">Gerado automaticamente em PDF — © 2025</p>
        </section>
        <div class="page-break"></div>
        HTML;
    }

    private function renderHeader(string $logoBase64, string $title): string
    {
        return <<<HTML
        <header class="institutional-header">
            <div class="header-left"><img src="{$logoBase64}" alt="Logo MeuApp"></div>
            <div class="header-right"><h1>MeuApp MVC</h1><span>{$title}</span></div>
        </header>
        HTML;
    }

    private function renderFooter(): string
    {
        return <<<HTML
        <footer class="pdf-footer text-center py-3 mt-auto">
            <small>Desenvolvido com Apache, PHP, MySql, Bootstrap + Twig em WSL</small>
            <p>© 2025 MeuApp MVC - Documentação do Projeto</p>
        </footer>        
        HTML;
    }

    private function renderSumario(array $sections): string
    {
        $html = "<section class='summary-page'><h2>📘 Sumário</h2><ul>";
        $i = 1;
        foreach ($sections as $slug) {
            $html .= "<li><a href='#{$slug}'>{$i}. " . ucfirst($slug) . "</a></li>";
            $i++;
        }
        $html .= "</ul></section><div class='page-break'></div>";
        return $html;
    }

    private function limparTemporarios(string $tmpDir): void
    {
        foreach (glob("{$tmpDir}/*.html") as $file) {
            @unlink($file);
        }
    }
    private function adicionarBookmarks(Dompdf $dompdf, string $tipo): void
    {
    // Nenhum suporte nativo a bookmarks — usamos anchors HTML
    if ($tipo !== 'configuracao') {
        $dompdf->getCanvas()->add_info("Title", $this->getTitulo($tipo));
        return;
    }

    // Apenas define metadados do documento (título, autor, etc.)
    $canvas = $dompdf->getCanvas();
    $canvas->add_info("Title", "MeuApp MVC - Documentação Técnica");
    $canvas->add_info("Author", "Lucio Flavio Lemos");
    $canvas->add_info("Subject", "Manual técnico do projeto MeuApp MVC");
    $canvas->add_info("Creator", "MeuApp MVC via Dompdf");
}



}
