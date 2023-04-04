<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Config;

use PhpLightning\Config\Backend\LnBitsBackendConfig;
use PhpLightning\Config\LightningConfig;
use PHPUnit\Framework\TestCase;

final class LightningConfigTest extends TestCase
{
    public function test_default_values(): void
    {
        $config = new LightningConfig();

        self::assertSame([
            'mode' => 'prod',
            'backends' => [],
        ], $config->jsonSerialize());
    }

    public function test_mode(): void
    {
        $config = (new LightningConfig())
            ->setMode('test');

        self::assertSame([
            'mode' => 'test',
            'backends' => [],
        ], $config->jsonSerialize());
    }

    public function test_domain_with_scheme(): void
    {
        $config = (new LightningConfig())
            ->setDomain('https://your-domain.com');

        self::assertSame([
            'mode' => 'prod',
            'backends' => [],
            'domain' => 'your-domain.com',
        ], $config->jsonSerialize());
    }

    public function test_domain_without_scheme(): void
    {
        $config = (new LightningConfig())
            ->setDomain('your-domain.com');

        self::assertSame([
            'mode' => 'prod',
            'backends' => [],
            'domain' => 'your-domain.com',
        ], $config->jsonSerialize());
    }

    public function test_receiver(): void
    {
        $config = (new LightningConfig())
            ->setReceiver('custom-receiver');

        self::assertSame([
            'mode' => 'prod',
            'backends' => [],
            'receiver' => 'custom-receiver',
        ], $config->jsonSerialize());
    }

    public function test_min_sendable(): void
    {
        $config = (new LightningConfig())
            ->setMinSendable(100_000);

        self::assertSame([
            'mode' => 'prod',
            'backends' => [],
            'min-sendable' => 100_000,
        ], $config->jsonSerialize());
    }

    public function test_max_sendable(): void
    {
        $config = (new LightningConfig())
            ->setMaxSendable(10_000_000_000);

        self::assertSame([
            'mode' => 'prod',
            'backends' => [],
            'max-sendable' => 10_000_000_000,
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

        self::assertSame([
            'mode' => 'prod',
            'backends' => [
                'lnbits' => [
                    'api_endpoint' => 'http://localhost:5000',
                    'api_key' => 'XYZ',
                ],
            ],
        ], $config->jsonSerialize());
    }
}
