<?php

declare(strict_types=1);

namespace PhpLightningTest\Feature;

use Gacela\Framework\Bootstrap\GacelaConfig;
use Gacela\Framework\Gacela;
use PhpLightning\Invoice\Infrastructure\Command\CallbackUrlCommand;
use PhpLightning\Shared\Value\SendableRange;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class GetCallbackUrlFeature extends TestCase
{
    public function test_default_values(): void
    {
        Gacela::bootstrap(__DIR__, static function (GacelaConfig $config): void {
            $config->resetInMemoryCache();
        });

        $tester = new CommandTester(new CallbackUrlCommand());
        $tester->execute([]);
        $outputAsJson = json_decode($tester->getDisplay(), true);

        self::assertEquals([
            'callback' => 'https://localhost/unknown-receiver',
            'minSendable' => 100000,
            'maxSendable' => 10000000000,
            'metadata' => '[["text/plain","Pay to unknown-receiver@localhost"],["text/identifier","unknown-receiver@localhost"]]',
            'tag' => 'payRequest',
            'commentAllowed' => false,
        ], $outputAsJson);
    }

    public function test_custom_config_values(): void
    {
        Gacela::bootstrap(__DIR__, static function (GacelaConfig $config): void {
            $config->resetInMemoryCache();
            $config->addAppConfigKeyValues([
                'domain' => 'custom-domain',
                'receiver' => 'custom-receiver',
                'sendable-range' => SendableRange::withMinMax(1_000, 2_000),
            ]);
        });

        $tester = new CommandTester(new CallbackUrlCommand());
        $tester->execute([]);
        $outputAsJson = json_decode($tester->getDisplay(), true);

        self::assertEquals([
            'callback' => 'https://custom-domain/custom-receiver',
            'minSendable' => 1_000,
            'maxSendable' => 2_000,
            'metadata' => '[["text/plain","Pay to custom-receiver@custom-domain"],["text/identifier","custom-receiver@custom-domain"]]',
            'tag' => 'payRequest',
            'commentAllowed' => false,
        ], $outputAsJson);
    }
}
