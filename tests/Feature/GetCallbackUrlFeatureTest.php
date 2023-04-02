<?php

declare(strict_types=1);

namespace PhpLightningTest\Feature;

use Gacela\Framework\Bootstrap\GacelaConfig;
use Gacela\Framework\Gacela;
use PhpLightning\Invoice\Infrastructure\Command\CallbackUrlCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class GetCallbackUrlFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        Gacela::bootstrap(__DIR__, static function (GacelaConfig $config): void {
            $config->resetInMemoryCache();
            $config->addAppConfigKeyValues([
                'domain' => 'custom-domain',
                'receiver' => 'custom-receiver',
            ]);
        });
    }

    public function test_default_values(): void
    {
        $tester = new CommandTester(new CallbackUrlCommand());
        $tester->execute([]);
        $outputAsJson = json_decode($tester->getDisplay(), true);

        self::assertEquals([
            'callback' => 'https://custom-domain/custom-receiver',
            'maxSendable' => 10_000_000_000,
            'minSendable' => 100_000,
            'metadata' => '[["text/plain","Pay to custom-receiver@custom-domain"],["text/identifier","custom-receiver@custom-domain"]]',
            'tag' => 'payRequest',
            'commentAllowed' => false,
        ], $outputAsJson);
    }
}
