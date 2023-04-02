<?php

declare(strict_types=1);

namespace PhpLightning\Console;

use Gacela\Console\ConsoleConfig;
use Gacela\Framework\AbstractDependencyProvider;
use Gacela\Framework\Container\Container;
use PhpLightning\Invoice\Infrastructure\Command\CallbackUrlCommand;

/**
 * @method ConsoleConfig getConfig()
 */
final class ConsoleDependencyProvider extends AbstractDependencyProvider
{
    public const COMMANDS = 'COMMANDS';

    public function provideModuleDependencies(Container $container): void
    {
        $this->addCommands($container);
    }

    private function addCommands(Container $container): void
    {
        $container->set(self::COMMANDS, static fn() => [
            new CallbackUrlCommand(),
        ]);
    }
}
