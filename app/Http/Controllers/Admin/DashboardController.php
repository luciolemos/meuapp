<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Support\Database;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $connectionOk = null;
        $connectionError = null;

        if (isset($_GET['test_db'])) {
            try {
                Database::connection();
                $connectionOk = true;
            } catch (\Throwable $e) {
                $connectionOk = false;
                $connectionError = $e->getMessage();
            }
        }

        $this->renderView('dashboard.twig', [
            'title' => 'Dashboard',
            'stats' => [
                'patients' => 124,
                'posts' => 56,
                'visits' => 3087
            ],
            'versions' => $this->collectVersions(),
            'connectionOk' => $connectionOk,
            'connectionError' => $connectionError,
            'test_db_active' => isset($_GET['test_db']),
        ]);
    }

    /**
     * Coleta dinamicamente versões de ferramentas do stack, sempre que disponíveis.
     */
    private function collectVersions(): array
    {
        $tools = [
            [
                'name' => 'PHP',
                'icon' => 'fab fa-php',
                'value' => PHP_VERSION,
                'description' => 'Interpretador utilizado pelo painel e pelo frontend.',
            ],
            [
                'name' => 'Composer',
                'icon' => 'fas fa-cubes',
                'value' => $this->detectVersion([
                    ['command' => 'composer --version 2>/dev/null', 'parser' => fn(string $out) => $this->extractSemver($out)],
                    ['command' => 'php composer.phar --version 2>/dev/null', 'parser' => fn(string $out) => $this->extractSemver($out)],
                ]),
                'description' => 'Gerenciador de dependências PHP.',
            ],
            [
                'name' => 'Node.js',
                'icon' => 'fab fa-node-js',
                'value' => $this->detectVersion([
                    ['command' => 'node --version 2>/dev/null', 'parser' => fn(string $out) => ltrim(trim($out), 'vV')],
                ]),
                'description' => 'Runtime para utilidades frontend e ferramenta de build.',
            ],
            [
                'name' => 'NPM',
                'icon' => 'fab fa-npm',
                'value' => $this->detectVersion([
                    ['command' => 'npm --version 2>/dev/null', 'parser' => fn(string $out) => trim($out)],
                ]),
                'description' => 'Gerencia pacotes JavaScript e scripts auxiliares.',
            ],
            [
                'name' => 'NVM',
                'icon' => 'fas fa-code-branch',
                'value' => $this->detectVersion([
                    ['command' => 'bash -lc "command -v nvm >/dev/null 2>&1 && nvm --version"', 'parser' => fn(string $out) => trim($out)],
                ]),
                'description' => 'Gerencia múltiplas versões do Node.js.',
            ],
            [
                'name' => 'Git',
                'icon' => 'fab fa-git-alt',
                'value' => $this->detectVersion([
                    ['command' => 'git --version 2>/dev/null', 'parser' => fn(string $out) => $this->extractSemver($out)],
                ]),
                'description' => 'Controle de versão distribuído utilizado no projeto.',
            ],
            [
                'name' => 'Apache HTTPD',
                'icon' => 'fas fa-server',
                'value' => $this->detectVersion([
                    ['command' => 'apache2ctl -v 2>/dev/null', 'parser' => fn(string $out) => $this->extractApacheVersion($out)],
                    ['command' => 'httpd -v 2>/dev/null', 'parser' => fn(string $out) => $this->extractApacheVersion($out)],
                ]),
                'description' => 'Servidor web responsável por entregar o MeuApp.',
            ],
            [
                'name' => 'MariaDB',
                'icon' => 'fas fa-database',
                'value' => $this->detectVersion([
                    ['command' => 'mysql --version 2>/dev/null', 'parser' => fn(string $out) => $this->extractSemver($out)],
                ]),
                'description' => 'Banco de dados relacional (caso habilitado).',
            ],
            [
                'name' => 'Sistema Operacional',
                'icon' => 'fab fa-linux',
                'value' => $this->detectOsName(),
                'description' => 'Distribuição base onde o MeuApp está em execução.',
            ],
        ];

        // Filtra apenas ferramentas com versão identificada
        return array_values(array_filter($tools, static function (array $tool): bool {
            return !empty($tool['value']);
        }));
    }

    /**
     * Executa uma série de comandos até encontrar uma versão válida.
     *
     * @param array<int, array{command:string, parser?:callable}> $attempts
     */
    private function detectVersion(array $attempts): ?string
    {
        foreach ($attempts as $attempt) {
            $command = $attempt['command'] ?? null;
            if (!$command) {
                continue;
            }

            $output = $this->runCommand($command);
            if ($output === null) {
                continue;
            }

            $parser = $attempt['parser'] ?? null;
            if (is_callable($parser)) {
                $parsed = $parser($output);
                if (!empty($parsed)) {
                    return $parsed;
                }
                continue;
            }

            if (!empty($output)) {
                return $output;
            }
        }

        return null;
    }

    private function runCommand(string $command): ?string
    {
        try {
            $output = @shell_exec($command);
            if (!is_string($output)) {
                return null;
            }
            $output = trim($output);
            return $output !== '' ? $output : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function extractSemver(string $output): ?string
    {
        if (preg_match('/\d+\.\d+(\.\d+)?/', $output, $matches)) {
            return $matches[0];
        }
        return null;
    }

    private function extractApacheVersion(string $output): ?string
    {
        if (preg_match('/Apache\/(\d+\.\d+(\.\d+)?)/', $output, $matches)) {
            return $matches[1];
        }
        return $this->extractSemver($output);
    }

    private function detectOsName(): ?string
    {
        $candidates = ['/etc/os-release', '/usr/lib/os-release'];
        foreach ($candidates as $file) {
            if (!is_readable($file)) {
                continue;
            }
            $contents = @file_get_contents($file);
            if ($contents === false) {
                continue;
            }
            if (preg_match('/^PRETTY_NAME="?(.+?)"?$/m', $contents, $matches)) {
                return trim($matches[1], "\"'");
            }
        }

        $fallback = trim(php_uname('s') . ' ' . php_uname('r'));
        return $fallback !== '' ? $fallback : null;
    }
}
