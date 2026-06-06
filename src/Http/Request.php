<?php

namespace App\Http;

use App\Support\AppPath;

class Request
{
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
        private readonly array $post,
        private readonly array $server,
    ) {}

    public static function capture(): self
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $path = AppPath::normalizeRequestPath($uri);

        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            $path,
            $_GET,
            $_POST,
            $_SERVER,
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->post);
    }

    public function boolean(string $key): bool
    {
        $value = $this->input($key);

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function validate(array $rules): array
    {
        $errors = [];
        $data = [];

        foreach ($rules as $field => $ruleString) {
            $value = trim((string) ($this->input($field) ?? ''));
            $data[$field] = $value;
            $rulesList = explode('|', $ruleString);

            foreach ($rulesList as $rule) {
                if ($rule === 'required' && $value === '') {
                    $errors[$field][] = 'فیلد الزامی است.';
                    break;
                }

                if (str_starts_with($rule, 'size:') && $value !== '') {
                    $size = (int) substr($rule, 5);
                    if (strlen($value) !== $size) {
                        $errors[$field][] = "باید {$size} کاراکتر باشد.";
                        break;
                    }
                }

                if ($rule === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'ایمیل نامعتبر است.';
                    break;
                }

                if (str_starts_with($rule, 'min:') && $value !== '') {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = "حداقل {$min} کاراکتر لازم است.";
                        break;
                    }
                }

                if (str_starts_with($rule, 'in:') && $value !== '') {
                    $allowed = explode(',', substr($rule, 3));
                    if (!in_array($value, $allowed, true)) {
                        $errors[$field][] = 'مقدار نامعتبر است.';
                        break;
                    }
                }
            }
        }

        if ($errors !== []) {
            remember_old($this->all());
            set_errors($errors);
            redirect($_SERVER['HTTP_REFERER'] ?? url('/'));
        }

        return $data;
    }
}
