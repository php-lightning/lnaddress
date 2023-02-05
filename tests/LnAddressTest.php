<?php

declare(strict_types=1);

namespace tests;

use PhpLightning\HttpApiInterface;
use PhpLightning\LnAddress;
use PHPUnit\Framework\TestCase;

final class LnAddressTest extends TestCase
{
    public function test_successful_payment_request_with_amount(): void
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
        $lnAddress = new LnAddress($httpApi);
        $lnAddress->generateInvoice($backend, $backend_options);

        self::expectOutputString(
            '{"pr":"any payment_request","status":"OK","successAction":{"tag":"message","message":"Payment received!"},"routes":[],"disposable":false}'
        );
    }

    public function test_successful_payment_request_without_amount(): void
    {
        $backend = 'lnbits';
        $backend_options = array();
        $backend_options['lnbits'] = [
            'api_endpoint' => 'http://localhost:5000',
            'api_key' => ''
        ];

        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/ping';
        $_GET['amount'] = 0;

        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(
            json_encode([
                'payment_request' => 'any payment_request',
            ])
        );
        $lnAddress = new LnAddress($httpApi);
        $lnAddress->generateInvoice($backend, $backend_options);

        self::expectOutputString(
            '{"callback":"https://localhost/ping","maxSendable":10000000000,"minSendable":100000,"metadata":"[[\"text/plain\",\"Pay to LnAddress@localhost\"],[\"text/identifier\",\"LnAddress@localhost\"]]","tag":"payRequest","commentAllowed":0}'
        );
    }

    public function test_error_payment_request_with_amount(): void
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
        $httpApi->method('get')->willReturn(null);
        $lnAddress = new LnAddress($httpApi);
        $lnAddress->generateInvoice($backend, $backend_options);

        self::expectOutputString(
            '{"status":"ERROR","reason":"Backend is unreachable"}'
        );
    }

    public function test_error_payment_request_without_amount(): void
    {
        $backend = 'lnbits';
        $backend_options = array();
        $backend_options['lnbits'] = [
            'api_endpoint' => 'http://localhost:5000',
            'api_key' => ''
        ];

        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/ping';
        $_GET['amount'] = 0;

        $httpApi = $this->createStub(HttpApiInterface::class);
        $httpApi->method('get')->willReturn(null);
        $lnAddress = new LnAddress($httpApi);
        $lnAddress->generateInvoice($backend, $backend_options);

        self::expectOutputString(
            '{"callback":"https://localhost/ping","maxSendable":10000000000,"minSendable":100000,"metadata":"[[\"text/plain\",\"Pay to LnAddress@localhost\"],[\"text/identifier\",\"LnAddress@localhost\"]]","tag":"payRequest","commentAllowed":0}'
        );
    }
}