<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Config;

use PhpLightning\Config\LightningConfig;
use PhpLightning\Shared\Value\SendableRange;
use PHPUnit\Framework\TestCase;

final class LightningConfigTest extends TestCase
{
    public function test_default_values(): void
    {
        $config = new LightningConfig();

        self::assertSame([], $config->jsonSerialize());
    }

    public function test_domain_with_scheme(): void
    {
        $config = (new LightningConfig())
            ->setDomain('https://your-domain.com');

        self::assertSame([
            'domain' => 'your-domain.com',
        ], $config->jsonSerialize());
    }

    public function test_domain_without_scheme(): void
    {
        $config = (new LightningConfig())
            ->setDomain('your-domain.com');

        self::assertSame([
            'domain' => 'your-domain.com',
        ], $config->jsonSerialize());
    }

    public function test_receiver(): void
    {
        $config = (new LightningConfig())
            ->setReceiver('custom-receiver');

        self::assertSame([
            'receiver' => 'custom-receiver',
        ], $config->jsonSerialize());
    }

    public function test_sendable_range(): void
    {
        $config = (new LightningConfig())
            ->setSendableRange(1_000, 5_000);

        self::assertEquals([
            'sendable-range' => SendableRange::withMinMax(1_000, 5_000),
        ], $config->jsonSerialize());
    }

    public function test_description_and_success_message(): void
    {
        $config = (new LightningConfig())
            ->setDescriptionTemplate('Pay to %s on example')
            ->setSuccessMessage('Thanks!');

        self::assertSame([
            'description-template' => 'Pay to %s on example',
            'success-message' => 'Thanks!',
        ], $config->jsonSerialize());
    }
}
