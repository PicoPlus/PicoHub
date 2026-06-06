<?php

namespace App\Http;

class View
{
    public static function render(string $template, array $data = []): string
    {
        $file = BASE_PATH . '/src/Views/' . str_replace('.', '/', $template) . '.php';

        if (!is_file($file)) {
            throw new \RuntimeException("View not found: {$template}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $file;

        return (string) ob_get_clean();
    }
}
