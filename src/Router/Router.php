<?php

declare(strict_types=1);

namespace PhpLightning\Router;

use function count;

final class Router
{
    /**
     * Eg: [method -> url -> [action, args]]
     *
     * @var array<string, array<string, array{
     *     action: callable,
     *     args?: array
     * }>>
     */
    private array $routes = [];

    private function __construct(
        private string $requestMethod,
        private string $requestUri,
    ) {
    }

    public static function withServer(array $server = []): self
    {
        return new self(
            (string)($server['REQUEST_METHOD'] ?? ''),
            (string)($server['REQUEST_URI'] ?? ''),
        );
    }

    public function listen(): void
    {
        $requestUrl = $this->requestUrl();

        $notFoundFn = static fn (): string => "404: route '{$requestUrl}' not found";

        $current = $this->routes[$this->requestMethod][$requestUrl]
            ?? ['action' => $notFoundFn, 'args' => []];

        /** @psalm-suppress TooManyArguments */
        echo (string)$current['action'](...$current['args'] ?? []);
    }

    public function get(string $route, callable $callback): void
    {
        $this->route('GET', $route, $callback);
    }

    private function route(string $method, string $route, callable $callback): void
    {
        $requestUrl = $this->requestUrl();
        $routeParts = explode('/', $route);
        $requestUrlParts = explode('/', $requestUrl);
        array_shift($routeParts);
        array_shift($requestUrlParts);

        if ($routeParts[0] === '' && count($requestUrlParts) === 0) {
            $this->routes[$method][$requestUrl] = [
                'action' => $callback,
                'args' => [],
            ];
            return;
        }

        if (count($routeParts) !== count($requestUrlParts)) {
            return;
        }

        $parameters = $this->parameters($routeParts, $requestUrlParts);
        $this->routes[$method][$requestUrl] = [
            'action' => $callback,
            'args' => $parameters,
        ];
    }

    private function requestUrl(): string
    {
        $requestUrl = (string)filter_var($this->requestUri, FILTER_SANITIZE_URL);
        $requestUrl = rtrim($requestUrl, '/');

        return (string)strtok($requestUrl, '?');
    }

    /**
     * @psalm-suppress MixedAssignment,MixedArgument
     */
    private function parameters(array $routeParts, array $requestUrlParts): array
    {
        $parameters = [];
        for ($i = 0, $iMax = count($routeParts); $i < $iMax; ++$i) {
            $routePart = $routeParts[$i];
            if (preg_match('/^[$]/', $routePart)) {
                $parameters[] = $requestUrlParts[$i];
            } elseif ($routeParts[$i] !== $requestUrlParts[$i]) {
                return [];
            }
        }

        return $parameters;
    }
}
