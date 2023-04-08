<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Router;

use PhpLightning\Router\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = Router::withServer([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/foo/?bar=123',
        ]);
    }

    public function test_404_no_route_found(): void
    {
        $this->router->listen();

        $this->expectOutputString("404: route '/foo' not found");
    }

    public function test_static_get_route_found(): void
    {
        $this->router->get('/foo', static function (): void {
            echo 'route found';
        });

        $this->router->listen();

        $this->expectOutputString('route found');
    }

    public function test_dynamic_get_route_found(): void
    {
        $this->router->get('/$name', static function (string $name): void {
            echo "route '{$name}' found";
        });

        $this->router->listen();

        $this->expectOutputString("route 'foo' found");
    }
}
