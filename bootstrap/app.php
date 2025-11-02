<?php
// =============================================
// 🧠 Bootstrap principal da aplicação
// =============================================

use App\Bootstrap\App;

// Autoload Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Inicializa Providers e Configs
$app = App::boot();
$app->run();
