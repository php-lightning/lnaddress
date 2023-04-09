<?php

declare(strict_types=1);

namespace PhpLightning\Router;

final class Router
{
    /**
     * @param class-string $controller
     */
    public static function get(
        string $path,
        string $controller,
        string $action = '__invoke',
    ): void {
        self::runRoute(Request::METHOD_GET, $path, $controller, $action);
    }

    /**
     * @param class-string $controller
     */
    public static function head(
        string $path,
        string $controller,
        string $action = '__invoke',
    ): void {
        self::runRoute(Request::METHOD_HEAD, $path, $controller, $action);
    }

    /**
     * @param class-string $controller
     */
    public static function connect(
        string $path,
        string $controller,
        string $action = '__invoke',
    ): void {
        self::runRoute(Request::METHOD_CONNECT, $path, $controller, $action);
    }

    /**
     * @param class-string $controller
     */
    public static function delete(
        string $path,
        string $controller,
        string $action = '__invoke',
    ): void {
        self::runRoute(Request::METHOD_DELETE, $path, $controller, $action);
    }

    /**
     * @param class-string $controller
     */
    public static function options(
        string $path,
        string $controller,
        string $action = '__invoke',
    ): void {
        self::runRoute(Request::METHOD_OPTIONS, $path, $controller, $action);
    }

    /**
     * @param class-string $controller
     */
    public static function patch(
        string $path,
        string $controller,
        string $action = '__invoke',
    ): void {
        self::runRoute(Request::METHOD_PATCH, $path, $controller, $action);
    }

    /**
     * @param class-string $controller
     */
    public static function post(
        string $path,
        string $controller,
        string $action = '__invoke',
    ): void {
        self::runRoute(Request::METHOD_POST, $path, $controller, $action);
    }

    /**
     * @param class-string $controller
     */
    public static function put(
        string $path,
        string $controller,
        string $action = '__invoke',
    ): void {
        self::runRoute(Request::METHOD_PUT, $path, $controller, $action);
    }

    /**
     * @param class-string $controller
     */
    public static function trace(
        string $path,
        string $controller,
        string $action = '__invoke',
    ): void {
        self::runRoute(Request::METHOD_TRACE, $path, $controller, $action);
    }

    /**
     * @param class-string $controller
     */
    private static function runRoute(
        string $method,
        string $path,
        string $controller,
        string $action = '__invoke',
    ): void {
        $route = new Route($method, $path, $controller, $action);

        if ($route->requestMatches()) {
            echo $route->run();
        }
    }
}
