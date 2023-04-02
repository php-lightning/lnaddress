<?php

declare(strict_types=1);

namespace PhpLightning\Console;

use Gacela\Console\ConsoleConfig;
use Gacela\Console\ConsoleDependencyProvider;
use Gacela\Framework\AbstractFactory;
use Symfony\Component\Console\Command\Command;

/**
 * @method ConsoleConfig getConfig()
 */
final class ConsoleFactory extends AbstractFactory
{
    /**
     * @return list<Command>
     */
    public function getConsoleCommands(): array
    {
        return (array)$this->getProvidedDependency(ConsoleDependencyProvider::COMMANDS);
    }
}
