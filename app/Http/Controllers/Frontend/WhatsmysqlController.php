<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\BaseController;

class WhatsmysqlController extends BaseController
{
    public function index(): void
    {
        $defaultPatientsSql = <<<'SQL'
CREATE TABLE `tbl_meuapp_patients` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `staff_id` VARCHAR(30) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `convenio` VARCHAR(80) NOT NULL DEFAULT 'particular',
    `situacao_cadastro` ENUM('ativo','incompleto','suspenso','cancelado') NOT NULL DEFAULT 'ativo',
    `avatar` VARCHAR(255) DEFAULT NULL,
    `birth_date` DATE DEFAULT NULL,
    `cpf` CHAR(11) DEFAULT NULL,
    `address` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_consulta_em` DATETIME DEFAULT NULL,
    `last_health_state` ENUM('leve','inspira_cuidados','grave','critico','terminal','recuperado','estavel') DEFAULT NULL,
    `last_cid` VARCHAR(10) DEFAULT NULL,
    `ultima_clinica` VARCHAR(150) DEFAULT NULL,
    INDEX `idx_tbl_meuapp_patients_convenio` (`convenio`),
    INDEX `idx_tbl_meuapp_patients_situacao` (`situacao_cadastro`),
    INDEX `idx_tbl_meuapp_patients_last_state` (`last_health_state`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
SQL;

        $defaultHealthSql = <<<'SQL'
CREATE TABLE `tbl_meuapp_health` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `consulta_data` DATETIME NOT NULL,
    `estado` ENUM('leve','inspira_cuidados','grave','critico','terminal','recuperado','estavel') NOT NULL DEFAULT 'leve',
    `diagnostico` VARCHAR(255) NOT NULL,
    `cid` VARCHAR(10) DEFAULT NULL,
    `tratamento` TEXT DEFAULT NULL,
    `prescricoes` TEXT DEFAULT NULL,
    `observacoes` TEXT DEFAULT NULL,
    `medico_responsavel` VARCHAR(150) DEFAULT NULL,
    `instituicao` VARCHAR(150) DEFAULT NULL,
    `pressao_arterial` VARCHAR(7) DEFAULT NULL,
    `frequencia_cardiaca` TINYINT UNSIGNED DEFAULT NULL,
    `temperatura_c` DECIMAL(4,1) DEFAULT NULL,
    `peso_kg` DECIMAL(5,2) DEFAULT NULL,
    `altura_m` DECIMAL(4,2) DEFAULT NULL,
    `proxima_consulta` DATETIME DEFAULT NULL,
    `status_tratamento` ENUM('em_andamento','concluido','suspenso') DEFAULT 'em_andamento',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_health_patient` (`user_id`),
    INDEX `idx_health_estado` (`estado`),
    INDEX `idx_health_cid` (`cid`),
    CONSTRAINT `fk_health_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `tbl_meuapp_patients` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
SQL;

        $pacientesSql = $this->loadSqlFile('pacientes.sql', $defaultPatientsSql);
        $atendimentosSql = $this->loadSqlFile('atendimentos.sql', $defaultHealthSql);

        $this->renderView('pages/whatsmysql.twig', [
            'title'       => 'O que é MySQL?',
            'subtitle'    => 'Fundamentos do banco de dados relacional que alimenta o MeuApp MVC',
            'description' => 'Conheça conceitos essenciais do MySQL, práticas de modelagem e acesse os scripts completos das tabelas de pacientes e atendimentos do MeuApp.',
            'author'      => 'Equipe MeuApp',
            'date'        => date('Y-m-d'),
            'meta' => [
            'description' => 'Artigo que explica MySQL, comandos práticos e os scripts SQL usados para pacientes e atendimentos no MeuApp MVC.',
            'keywords'    => 'mysql, banco de dados, tbl_meuapp_patients, tbl_meuapp_health, sql, meuapp mvc',
            ],
            'layout' => [
                'hero' => true,
                'feature_cards' => true,
                'metrics'       => false,
                'cta_banner'    => true,
                'faq'           => true,
                'newsletter'    => false,
            ],
            'hero_actions' => [
                [
                    'label'   => 'Estrutura da aplicação',
                    'href'    => '/documentacao/estrutura',
                    'icon'    => 'fas fa-sitemap',
                    'variant' => 'light',
                ],
                [
                    'label'   => 'Guia de rotas',
                    'href'    => '/documentacao/rotas',
                    'icon'    => 'fas fa-route',
                    'variant' => 'warning',
                ],
            ],
            'pacientes_sql' => $pacientesSql,
            'atendimentos_sql' => $atendimentosSql,
            'mysql_highlights' => [
                [
                    'icon' => 'fas fa-database',
                    'title' => 'Armazenamento relacional sólido',
                    'description' => 'Assegura integridade referencial, índices eficientes e suporte a transações ACID.',
                ],
                [
                    'icon' => 'fas fa-lock',
                    'title' => 'Segurança e controle de acesso',
                    'description' => 'Permite gerenciamento de usuários, roles e concessões de privilégios granulares.',
                ],
                [
                    'icon' => 'fas fa-chart-line',
                    'title' => 'Escalabilidade comprovada',
                    'description' => 'Equipa projetos de qualquer porte com replicação, particionamento e utilitários de backup.',
                ],
                [
                    'icon' => 'fas fa-tools',
                    'title' => 'Ferramentas maduras',
                    'description' => 'Oferece ecossistema amplo, como MySQL Workbench, CLI e integração com frameworks PHP.',
                ],
            ],
            'faq_items' => [
                [
                    'question' => 'Posso usar outro banco além do MySQL?',
                    'answer'   => 'Sim. A camada de repositório do MeuApp utiliza PDO, permitindo adaptação para MariaDB, PostgreSQL ou SQLite com ajustes mínimos na configuração.',
                ],
                [
                    'question' => 'Preciso criar a tabela manualmente sempre?',
                    'answer'   => 'Não necessariamente. Você pode rodar o script uma vez, automatizar via migrations, ou adaptar ferramentas como Doctrine Migrations e Phinx.',
                ],
                [
                    'question' => 'Como popular dados de teste?',
                    'answer'   => 'Utilize comandos `INSERT`, scripts seeders personalizados ou o painel Admin para criar usuários com avatar e atributos adicionais.',
                ],
            ],
        ]);
    }

    private function loadSqlFile(string $relativePath, string $fallback): string
    {
        $basePath = dirname(__DIR__, 3) . '/' . ltrim($relativePath, '/');
        if (is_file($basePath) && is_readable($basePath)) {
            $contents = file_get_contents($basePath);
            if ($contents !== false && trim($contents) !== '') {
                return $contents;
            }
        }

        return $fallback;
    }
}
