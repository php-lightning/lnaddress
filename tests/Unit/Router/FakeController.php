<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Router;

final class FakeController
{
    public function basicAction(): string
    {
        return 'Expected!';
    }

    public function stringParamAction(string $param): string
    {
        $type = get_debug_type($param);
        return "The '{$type}' param is '{$param}'!";
    }

    public function intParamAction(int $param): string
    {
        $type = get_debug_type($param);
        return "The '{$type}' param is '{$param}'!";
    }

    public function floatParamAction(float $param): string
    {
        $type = get_debug_type($param);
        return "The '{$type}' param is '{$param}'!";
    }

    public function boolParamAction(bool $param): string
    {
        $type = get_debug_type($param);
        $stringParam = json_encode($param);
        return "The '{$type}' param is '{$stringParam}'!";
    }

    public function manyParamsAction(string $firstParam, string $secondParam, string $thirdParam): string
    {
        return "The params are '{$firstParam}', '{$secondParam}' and '{$thirdParam}'!";
    }
}
