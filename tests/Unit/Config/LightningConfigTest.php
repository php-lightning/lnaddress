<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Config;

use PhpLightning\Config\Backend\LnBitsBackendConfig;
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

    public function test_mode(): void
    {
        $config = (new LightningConfig())
            ->setMode('test');

        self::assertSame([
            'mode' => 'test',
        ], $config->jsonSerialize());
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

    public function test_ln_bits_backend(): void
    {
        $config = (new LightningConfig())
            ->addBackend(
                (new LnBitsBackendConfig())
                    ->setApiEndpoint('http://localhost:5000')
                    ->setApiKey('XYZ'),
            );

        self::assertEquals([
            'backends' => [
                'lnbits' => [
                    'api_endpoint' => 'http://localhost:5000',
                    'api_key' => 'XYZ',
                ],
            ],
        ], $config->jsonSerialize());
    }
}
