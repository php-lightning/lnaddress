<?php

declare(strict_types=1);

namespace tests;

use PhpLightning\HttpApiInterface;
use PhpLightning\LnAddress;
use PHPUnit\Framework\TestCase;

final class LnAddressTest extends TestCase
{
    private const BACKEND = 'lnbits';

    public function test_unknown_backend(): void
    {
        $backend_options = [
            self::BACKEND => [
                'api_endpoint' => 'http://localhost:5000',
                'api_key' => ''
            ]
        ];

        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/ping';
        $_GET['amount'] = 123456;

        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(null);
        $lnAddress = new LnAddress($httpApi);
        $lnAddress->generateInvoice('unknow', $backend_options);

        $this->expectOutputString(
            json_encode([
                'status' => 'ERROR',
                'reason' => 'Backend is unreachable',
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_successful_payment_request_with_amount(): void
    {
        $backend_options = [
            self::BACKEND => [
                'api_endpoint' => 'http://localhost:5000',
                'api_key' => ''
            ]
        ];

        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/ping';
        $_GET['amount'] = 123456;

        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(
            json_encode([
                'payment_request' => 'any payment_request',
            ], JSON_THROW_ON_ERROR)
        );
        $lnAddress = new LnAddress($httpApi);
        $lnAddress->generateInvoice(self::BACKEND, $backend_options);

        $this->expectOutputString(
            json_encode([
                "pr" => "any payment_request",
                "status" => "OK",
                "successAction" => [
                    "tag" => "message",
                    "message" => "Payment received!",
                ],
                "routes" => [],
                "disposable" => false,
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_successful_payment_request_with_amount_not_ok(): void
    {
        $backend_options = [
            self::BACKEND => [
                'api_endpoint' => 'http://localhost:5000',
                'api_key' => ''
            ]
        ];

        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/ping';
        $_GET['amount'] = 123456;

        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(
            json_encode([
                'payment_request' => 'any payment_request',
            ], JSON_THROW_ON_ERROR)
        );
        $lnAddress = new LnAddress($httpApi);
        $lnAddress->generateInvoice('unknown', $backend_options);

        $this->expectOutputString(
            json_encode([
                "status" => "ERROR",
                "reason" => "Backend is unreachable",
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_successful_payment_request_without_amount(): void
    {
        $backend_options = [
            self::BACKEND => [
                'api_endpoint' => 'http://localhost:5000',
                'api_key' => ''
            ]
        ];

        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/ping';
        $_GET['amount'] = 0;

        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(
            json_encode([
                'payment_request' => 'any payment_request',
            ], JSON_THROW_ON_ERROR)
        );
        $lnAddress = new LnAddress($httpApi);
        $lnAddress->generateInvoice(self::BACKEND, $backend_options);

        $this->expectOutputString(
            json_encode([
                'callback' => 'https://localhost/ping',
                'maxSendable' => 10000000000,
                'minSendable' => 100000,
                'metadata' => json_encode([
                    ['text/plain', 'Pay to LnAddress@localhost'],
                    ['text/identifier', 'LnAddress@localhost'],
                ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
                'tag' => 'payRequest',
                'commentAllowed' => 0,
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)
        );
    }

    public function test_error_payment_request_with_amount(): void
    {
        $backend_options = [
            self::BACKEND => [
                'api_endpoint' => 'http://localhost:5000',
                'api_key' => ''
            ]
        ];

        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/ping';
        $_GET['amount'] = 123456;

        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(null);
        $lnAddress = new LnAddress($httpApi);
        $lnAddress->generateInvoice(self::BACKEND, $backend_options);

        $this->expectOutputString(
            json_encode([
                'status' => 'ERROR',
                'reason' => 'Backend is unreachable',
            ], JSON_THROW_ON_ERROR)
        );
    }

    public function test_error_payment_request_without_amount(): void
    {
        $backend_options = [
            self::BACKEND => [
                'api_endpoint' => 'http://localhost:5000',
                'api_key' => ''
            ]
        ];

        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/ping';
        $_GET['amount'] = 0;

        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(null);
        $lnAddress = new LnAddress($httpApi);
        $lnAddress->generateInvoice(self::BACKEND, $backend_options);

        $this->expectOutputString(
            json_encode([
                'callback' => 'https://localhost/ping',
                'maxSendable' => 10000000000,
                'minSendable' => 100000,
                'metadata' => json_encode([
                    ['text/plain', 'Pay to LnAddress@localhost'],
                    ['text/identifier', 'LnAddress@localhost'],
                ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
                'tag' => 'payRequest',
                'commentAllowed' => 0,
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)
        );
    }
}