<?php

namespace App\Support;

class Logger
{
    public static function info(string $channel, string $message, array $context = []): void
    {
        self::write($channel, 'INFO', $message, $context);
    }

    public static function warning(string $channel, string $message, array $context = []): void
    {
        self::write($channel, 'WARNING', $message, $context);
    }

    public static function error(string $channel, string $message, array $context = []): void
    {
        self::write($channel, 'ERROR', $message, $context);
    }

    public static function debug(string $channel, string $message, array $context = []): void
    {
        if (!config('app.debug', false)) {
            return;
        }

        self::write($channel, 'DEBUG', $message, $context);
    }

    private static function write(string $channel, string $level, string $message, array $context): void
    {
        $dir = STORAGE_PATH . '/logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $line = json_encode([
            'time' => date('c'),
            'level' => $level,
            'channel' => $channel,
            'message' => $message,
            'context' => $context,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        file_put_contents($dir . '/' . $channel . '.log', $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
