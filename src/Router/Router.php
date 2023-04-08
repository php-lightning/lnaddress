<?php

declare(strict_types=1);

namespace PhpLightning\Router;

use function call_user_func_array;
use function count;

final class Router
{
    public function __construct(
        private string $requestMethod,
        private string $requestUri,
    ) {
    }

    public function get(string $route, callable $callback): void
    {
        if ($this->requestMethod === 'GET') {
            $this->route($route, $callback);
        }
    }

    private function route(string $route, callable $callback): void
    {
        $requestUrl = $this->requestUrl();
        $routeParts = explode('/', $route);
        $requestUrlParts = explode('/', $requestUrl);
        array_shift($routeParts);
        array_shift($requestUrlParts);

        if ($routeParts[0] === '' && count($requestUrlParts) === 0) {
            call_user_func_array($callback, []);
            exit();
        }

        if (count($routeParts) !== count($requestUrlParts)) {
            return;
        }

        $parameters = $this->parameters($routeParts, $requestUrlParts);
        if ($parameters === []) {
            return;
        }
        call_user_func_array($callback, $parameters);
        exit();
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
                $routePart = ltrim($routePart, '$');
                $parameters[] = $requestUrlParts[$i];
                $$routePart = $requestUrlParts[$i];
            } elseif ($routeParts[$i] !== $requestUrlParts[$i]) {
                return [];
            }
        }

        return $parameters;
    }
}
