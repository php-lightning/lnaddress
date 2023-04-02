<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Infrastructure\Command;

use Gacela\Framework\DocBlockResolverAwareTrait;
use PhpLightning\Invoice\InvoiceFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method InvoiceFacade getFacade()
 */
final class CallbackUrlCommand extends Command
{
    use DocBlockResolverAwareTrait;

    protected function configure(): void
    {
        $this->setName('callback-url')
            ->setDescription('Get a callback_url');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $callbackUrl = $this->getFacade()->getCallbackUrl();

        $json = json_encode($callbackUrl, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        $output->writeln($json);

        return self::SUCCESS;
    }
}