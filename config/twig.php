<?php

return [
    'paths' => [
        realpath(__DIR__ . '/../resources/views'),
    ],
    'cache' => realpath(__DIR__ . '/../storage/cache/twig'),
    'debug' => true,
    'auto_reload' => true
];
