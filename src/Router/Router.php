<?php

declare(strict_types=1);

namespace PhpLightning\Router;

use function count;
use function is_callable;

final class Router
{
    /**
     * Eg: [method -> url -> [action, args]]
     *
     * @var array<string, array<string, array{
     *     controller: string|callable,
     *     action: string,
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
            ?? ['controller' => $notFoundFn, 'action' => '', 'args' => []];

        if (is_callable($current['controller'])) {
            /** @psalm-suppress TooManyArguments */
            echo (string)$current['controller'](...$current['args'] ?? []);
        } else {
            /** @psalm-suppress TooManyArgument,InvalidStringClass,PossiblyUndefinedArrayOffset */
            echo (string)(new $current['controller']())
                ->{$current['action']}(...$current['args'] ?? []);
        }
    }

    public function get(
        string $route,
        callable|string $controller,
        string $action = '__invoke',
    ): void {
        $this->route('GET', $route, $controller, $action);
    }

    private function route(
        string $method,
        string $route,
        callable|string $controller,
        string $action = '',
    ): void {
        $requestUrl = $this->requestUrl();
        $requestUrlParts = explode('/', $requestUrl);
        $routeParts = explode('/', $route);

        if (count($routeParts) !== count($requestUrlParts)) {
            return;
        }

        array_shift($routeParts);
        array_shift($requestUrlParts);

        if ($routeParts[0] === '' && count($requestUrlParts) === 0) {
            $this->routes[$method][$requestUrl] = [
                'controller' => $controller,
                'action' => $action,
                'args' => [],
            ];
        } else {
            $this->routes[$method][$requestUrl] = [
                'controller' => $controller,
                'action' => $action,
                'args' => $this->parameters($routeParts, $requestUrlParts),
            ];
        }
    }

    private function requestUrl(): string
    {
        $requestUrl = (string)filter_var($this->requestUri, FILTER_SANITIZE_URL);
        $requestUrl = (string)strtok($requestUrl, '?');
        return rtrim($requestUrl, '/') ?: '/';
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
