<?php

declare(strict_types=1);

namespace tests;

use PhpLightning\HttpApiInterface;
use PhpLightning\LnAddress;
use PHPUnit\Framework\TestCase;

final class LnAddressTest extends TestCase
{
    public function test_successful_payment_request(): void
    {
        $backend = 'lnbits';
        $backend_options = array();
        $backend_options['lnbits'] = [
            'api_endpoint' => 'http://localhost:5000',
            'api_key' => ''
        ];

        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/ping';
        $_GET['amount'] = 123456;

        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(
            json_encode([
                'payment_request' => 'any payment_request',
            ])
        );
        $lnAddress = new LnAddress(
            $httpApi
        );
        $lnAddress->generateInvoice($backend, $backend_options);

        self::expectOutputString(
            '{"pr":"any payment_request","status":"OK","successAction":{"tag":"message","message":"Payment received!"},"routes":[],"disposable":false}'
        );
    }
}