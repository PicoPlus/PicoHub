<?php

namespace App\Http;

class Router
{
    /** @var array<int, array{methods: string[], path: string, handler: callable, middleware: string[]}> */
    private array $routes = [];

    public function get(string $path, callable $handler, array $middleware = []): void
    {
        $this->add(['GET'], $path, $handler, $middleware);
    }

    public function post(string $path, callable $handler, array $middleware = []): void
    {
        $this->add(['POST'], $path, $handler, $middleware);
    }

    public function add(array $methods, string $path, callable $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'methods' => array_map('strtoupper', $methods),
            'path' => rtrim($path, '/') ?: '/',
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request): void
    {
        foreach ($this->routes as $route) {
            if (!in_array($request->method(), $route['methods'], true)) {
                continue;
            }

            if ($route['path'] !== $request->path()) {
                continue;
            }

            foreach ($route['middleware'] as $middleware) {
                $middleware::handle($request);
            }

            if ($request->method() === 'POST') {
                $token = $request->input('_token');
                if (!$token || !hash_equals(csrf_token(), (string) $token)) {
                    http_response_code(419);
                    echo 'Invalid CSRF token';
                    return;
                }
            }

            $response = ($route['handler'])($request);

            if (is_string($response)) {
                echo $response;
            }

            return;
        }

        http_response_code(404);
        echo '404 Not Found';
    }
}
