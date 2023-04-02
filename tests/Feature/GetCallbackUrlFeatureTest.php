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
        Gacela::bootstrap(__DIR__, static function (GacelaConfig $config) {
            $config->resetInMemoryCache();
        });
    }

    public function test_default_values(): void
    {
        $tester = new CommandTester(new CallbackUrlCommand());
        $tester->execute([]);
        $outputAsJson = json_decode($tester->getDisplay(), true);

        self::assertEquals([
            "callback" => "https://localhost/ping",
            "maxSendable" => 10000000000,
            "minSendable" => 100000,
            "metadata" => "[[\"text/plain\",\"Pay to FileBaseNameLnAddressGenerator@localhost\"],[\"text/identifier\",\"FileBaseNameLnAddressGenerator@localhost\"]]",
            "tag" => "payRequest",
            "commentAllowed" => false
        ], $outputAsJson);
    }
}