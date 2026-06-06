<?php

function config(string $key, mixed $default = null): mixed
{
    $config = $GLOBALS['config'] ?? [];
    $segments = explode('.', $key);
    $value = $config;

    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}

function view(string $template, array $data = []): string
{
    return \App\Http\View::render($template, $data);
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function url(string $path = ''): string
{
    return \App\Support\AppPath::url($path);
}

function asset(string $path): string
{
    return url('/' . ltrim($path, '/'));
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function session(string $key, mixed $default = null): mixed
{
    return $_SESSION[$key] ?? $default;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function flash_messages(): array
{
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);

    return $messages;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['_flash'][$type] = $message;
}

function set_errors(array $errors): void
{
    $_SESSION['_errors'] = $errors;
}

function errors(): array
{
    $errors = $_SESSION['_errors'] ?? [];
    unset($_SESSION['_errors']);

    return $errors;
}

function error(string $field): ?string
{
    $errors = $_SESSION['_errors'] ?? [];

    return $errors[$field][0] ?? null;
}

function has_error(string $field): bool
{
    return error($field) !== null;
}

function remember_old(array $input): void
{
    $_SESSION['_old'] = $input;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}
