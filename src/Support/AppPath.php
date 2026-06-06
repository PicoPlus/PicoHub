<?php

namespace App\Support;

class AppPath
{
    private static ?string $basePath = null;

    public static function basePath(): string
    {
        if (self::$basePath !== null) {
            return self::$basePath;
        }

        $configured = trim((string) ($_ENV['APP_BASE_PATH'] ?? ''), '/');
        if ($configured !== '') {
            self::$basePath = '/' . $configured;

            return self::$basePath;
        }

        $fromFilesystem = self::detectFromFilesystem();
        if ($fromFilesystem !== '') {
            self::$basePath = $fromFilesystem;

            return self::$basePath;
        }

        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $dir = rtrim(dirname($scriptName), '/');

        if ($dir !== '' && $dir !== '/' && $dir !== '.') {
            self::$basePath = $dir;

            return self::$basePath;
        }

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        if (preg_match('#^(/[^/]+)/public(?:/|$)#', $uri, $matches)) {
            self::$basePath = $matches[1] . '/public';

            return self::$basePath;
        }
        if (str_starts_with($uri, '/public')) {
            self::$basePath = '/public';

            return self::$basePath;
        }

        self::$basePath = '';

        return self::$basePath;
    }

    public static function normalizeRequestPath(string $uri): string
    {
        $path = $uri;

        $base = self::basePath();
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }

        if ($path === '/index.php' || str_ends_with($path, '/index.php')) {
            $path = substr($path, 0, -strlen('/index.php')) ?: '/';
        }

        return rtrim($path, '/') ?: '/';
    }

    public static function url(string $path = ''): string
    {
        $path = '/' . ltrim($path, '/');
        $base = self::basePath();
        $relative = ($base !== '' ? $base : '') . $path;

        $appUrl = rtrim((string) config('app.url', ''), '/');
        if ($appUrl === '') {
            return $relative;
        }

        if ($base !== '' && str_ends_with($appUrl, $base)) {
            return $appUrl . $path;
        }

        if ($base !== '') {
            return $appUrl . $base . $path;
        }

        return $appUrl . $path;
    }

    private static function detectFromFilesystem(): string
    {
        $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
        $publicDir = realpath(defined('BASE_PATH') ? BASE_PATH . '/public' : dirname(__DIR__, 2) . '/public');

        if ($docRoot === false || $publicDir === false) {
            return '';
        }

        $docRoot = str_replace('\\', '/', $docRoot);
        $publicDir = str_replace('\\', '/', $publicDir);

        if (!str_starts_with($publicDir, $docRoot)) {
            return '';
        }

        $relative = substr($publicDir, strlen(rtrim($docRoot, '/')));

        return rtrim(str_replace('\\', '/', $relative), '/') ?: '';
    }
}
