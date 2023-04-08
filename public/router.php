<?php

declare(strict_types=1);

session_start();

function get(string $route, callable $callback): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        route($route, $callback);
    }
}

function route(string $route, callable $callback): void
{
    $request_url = (string)filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
    $request_url = rtrim($request_url, '/');
    $request_url = (string)strtok($request_url, '?');

    $route_parts = explode('/', $route);
    $request_url_parts = explode('/', $request_url);
    array_shift($route_parts);
    array_shift($request_url_parts);

    if ($route_parts[0] === '' && count($request_url_parts) === 0) {
        call_user_func_array($callback, []);
        exit();
    }

    if (count($route_parts) !== count($request_url_parts)) {
        return;
    }

    $parameters = [];
    for ($i = 0, $iMax = count($route_parts); $i < $iMax; $i++) {
        $route_part = $route_parts[$i];
        if (preg_match("/^[$]/", $route_part)) {
            $route_part = ltrim($route_part, '$');
            $parameters[] = $request_url_parts[$i];
            $$route_part = $request_url_parts[$i];
        } else if ($route_parts[$i] !== $request_url_parts[$i]) {
            return;
        }
    }
    call_user_func_array($callback, $parameters);
    exit();
}
