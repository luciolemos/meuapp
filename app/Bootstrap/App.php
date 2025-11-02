<?php
namespace App\Bootstrap;

use App\Core\App as CoreApp;

class App
{
    /**
     * Conjunto consolidado de configurações carregadas na fase de boot.
     *
     * @var array<string, mixed>
     */
    private array $config;

    private function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Realiza o bootstrap da aplicação (carrega configs, providers, etc.).
     */
    public static function boot(?array $config = null): self
    {
        $config ??= self::loadConfiguration();

        CoreApp::bootstrap();

        $instance = new self($config);
        $instance->registerProviders();

        return $instance;
    }

    /**
     * Dispara o ciclo principal da aplicação.
     */
    public function run(): void
    {
        CoreApp::run();
    }

    /**
     * Acessa as configurações carregadas.
     */
    public function config(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        return $this->config[$key] ?? $default;
    }

    /**
     * Carrega arquivos de configuração base.
     *
     * @return array<string, mixed>
     */
    private static function loadConfiguration(): array
    {
        $basePath = dirname(__DIR__, 2);

        $app = require $basePath . '/config/app.php';
        $twig = require $basePath . '/config/twig.php';

        return [
            'app' => $app,
            'twig' => $twig,
        ];
    }

    /**
     * Ponto central para registrar service providers.
     * (Futuro: executar AppServiceProvider, RouteServiceProvider, etc.)
     */
    private function registerProviders(): void
    {
        // Nenhum provider registrado ainda.
    }
}
