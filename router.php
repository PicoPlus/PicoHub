<?php

declare(strict_types=1);

// Dev router when document root is the project folder:
//   php -S localhost:8000 router.php

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if (preg_match('#^/public/(.*)$#', $uri, $matches)) {
    $_SERVER['SCRIPT_NAME'] = '/public/index.php';
    $_SERVER['REQUEST_URI'] = '/public/' . ($matches[1] !== '' ? $matches[1] : '');
    require __DIR__ . '/public/index.php';

    return true;
}

if ($uri === '/public' || $uri === '/public/') {
    $_SERVER['SCRIPT_NAME'] = '/public/index.php';
    $_SERVER['REQUEST_URI'] = '/public/';
    require __DIR__ . '/public/index.php';

    return true;
}

$static = __DIR__ . $uri;
if ($uri !== '/' && is_file($static)) {
    return false;
}

header('Location: /public/');
exit;
