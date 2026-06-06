<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');

require_once BASE_PATH . '/src/Support/Env.php';
require_once BASE_PATH . '/src/Support/helpers.php';

\App\Support\Env::load(BASE_PATH . '/.env');

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = BASE_PATH . '/src/' . $relative . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$GLOBALS['config'] = require BASE_PATH . '/config/config.php';
$GLOBALS['base_path'] = \App\Support\AppPath::basePath();
