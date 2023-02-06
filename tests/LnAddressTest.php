<?php

declare(strict_types=1);

namespace tests;

use PhpLightning\ConfigInterface;
use PhpLightning\HttpApiInterface;
use PhpLightning\LnAddress;
use PHPUnit\Framework\TestCase;

final class LnAddressTest extends TestCase
{
    private const BACKEND = 'lnbits';

    public function test_unknown_backend(): void
    {
        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(null);

        $lnAddress = new LnAddress($httpApi, $this->stubConfig());
        $actual = $lnAddress->generateInvoice(123456, 'unknown?');

        self::assertSame([
            'status' => 'ERROR',
            'reason' => 'Unknown Backend: unknown?',
        ], $actual);
    }

    public function test_successful_payment_request_with_amount(): void
    {
        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(
            json_encode([
                'payment_request' => 'any payment_request',
            ], JSON_THROW_ON_ERROR),
        );

        $lnAddress = new LnAddress($httpApi, $this->stubConfig());
        $actual = $lnAddress->generateInvoice(123456, self::BACKEND);

        self::assertSame([
            'pr' => 'any payment_request',
            'status' => 'OK',
            'successAction' => [
                'tag' => 'message',
                'message' => 'Payment received!',
            ],
            'routes' => [],
            'disposable' => false,
        ], $actual);
    }

    public function test_successful_payment_request_without_amount(): void
    {
        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(
            json_encode([
                'payment_request' => 'any payment_request',
            ], JSON_THROW_ON_ERROR),
        );

        $lnAddress = new LnAddress($httpApi, $this->stubConfig());
        $actual = $lnAddress->generateInvoice(0, self::BACKEND);

        self::assertSame([
            'callback' => 'https://localhost/ping',
            'maxSendable' => LnAddress::MAX_SENDABLE,
            'minSendable' => LnAddress::MIN_SENDABLE,
            'metadata' => json_encode([
                ['text/plain', 'Pay to LnAddress@localhost'],
                ['text/identifier', 'LnAddress@localhost'],
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            'tag' => 'payRequest',
            'commentAllowed' => 0,
        ], $actual);
    }

    public function test_error_payment_request_with_amount(): void
    {
        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(null);

        $lnAddress = new LnAddress($httpApi, $this->stubConfig());
        $actual = $lnAddress->generateInvoice(123456, self::BACKEND);

        self::assertSame([
            'status' => 'ERROR',
            'reason' => 'Backend "LnBits" unreachable',
        ], $actual);
    }

    public function test_error_payment_request_without_amount(): void
    {
        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(null);

        $lnAddress = new LnAddress($httpApi, $this->stubConfig());
        $actual = $lnAddress->generateInvoice(0, self::BACKEND);

        self::assertSame([
            'callback' => 'https://localhost/ping',
            'maxSendable' => LnAddress::MAX_SENDABLE,
            'minSendable' => LnAddress::MIN_SENDABLE,
            'metadata' => json_encode([
                ['text/plain', 'Pay to LnAddress@localhost'],
                ['text/identifier', 'LnAddress@localhost'],
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            'tag' => 'payRequest',
            'commentAllowed' => 0,
        ], $actual);
    }

    private function stubConfig(): ConfigInterface
    {
        $config = $this->createStub(ConfigInterface::class);
        $config->method('getHttpHost')->willReturn('localhost');
        $config->method('getRequestUri')->willReturn('/ping');
        $config->method('getBackendOptionsFor')->willReturn($this->backendOptions());

        return $config;
    }

    /**
     * @return array{
     *   lnbits: array{
     *     api_endpoint: string,
     *     api_key?: string,
     *   }
     */
    private function backendOptions(): array
    {
        return [
            'api_endpoint' => 'http://localhost:5000',
            'api_key' => '',
        ];
    }
}
