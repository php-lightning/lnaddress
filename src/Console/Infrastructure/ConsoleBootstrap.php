<?php

declare(strict_types=1);

namespace PhpLightning\Console\Infrastructure;

use Gacela\Framework\DocBlockResolverAwareTrait;
use PhpLightning\Console\ConsoleFactory;
use Symfony\Component\Console\Application;

/**
 * @method ConsoleFactory getFactory()
 */
final class ConsoleBootstrap extends Application
{
    use DocBlockResolverAwareTrait;

    protected function getDefaultCommands(): array
    {
        $commands = parent::getDefaultCommands();

        foreach ($this->getFactory()->getConsoleCommands() as $command) {
            $commands[$command->getName()] = $command;
        }

        return $commands;
    }
}
