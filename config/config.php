<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Configurações da Aplicação
    |--------------------------------------------------------------------------
    */
    'app_name' => 'MeuApp MVC',
    'app_env'  => 'development', // mude para 'production' ao implantar
    'app_debug' => true,         // exibe erros detalhados (falso em produção)

    /*
    |--------------------------------------------------------------------------
    | Configurações de Banco de Dados
    |--------------------------------------------------------------------------
    */
    'db' => [
        'driver'  => 'mysql',
        'host'    => 'localhost',
        'port'    => 3306,
        'name'    => 'meuapp',
        'user'    => 'luciolemos',
        'pass'    => 'root',
        'charset' => 'utf8mb4',
    ],
];
