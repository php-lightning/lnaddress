<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Config;

use PhpLightning\Config\Backend\LnBitsBackendConfig;
use PhpLightning\Config\LightningConfig;
use PhpLightning\Shared\Value\SendableRange;
use PHPUnit\Framework\TestCase;
use RuntimeException;

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

    public function test_add_backend_programmatically_without_a_file(): void
    {
        $config = (new LightningConfig())
            ->addBackend('bob', LnBitsBackendConfig::withEndpointAndKey('http://localhost:5000', 'key-123'));

        self::assertSame([
            'backends' => [
                'bob' => [
                    'api_endpoint' => 'http://localhost:5000',
                    'api_key' => 'key-123',
                ],
            ],
        ], $config->jsonSerialize());
    }

    public function test_add_backends_file_reports_missing_path(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Backends file not found: "/does/not/exist.json"');

        (new LightningConfig())->addBackendsFile('/does/not/exist.json');
    }

    public function test_add_backends_file_reports_missing_type(): void
    {
        $path = (string)tempnam(sys_get_temp_dir(), 'lnaddr');
        file_put_contents($path, (string)json_encode(['bob' => ['api_key' => 'x']]));

        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('Missing "type" for backend "bob"');
            (new LightningConfig())->addBackendsFile($path);
        } finally {
            unlink($path);
        }
    }
}
