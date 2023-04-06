<?php

declare(strict_types=1);

namespace PhpLightningTest\Feature;

use Gacela\Framework\AbstractDependencyProvider;
use Gacela\Framework\Bootstrap\GacelaConfig;
use Gacela\Framework\ClassResolver\GlobalInstance\AnonymousGlobal;
use Gacela\Framework\Container\Container;
use Gacela\Framework\Gacela;
use PhpLightning\Config\Backend\LnBitsBackendConfig;
use PhpLightning\Config\LightningConfig;
use PhpLightning\Invoice\InvoiceDependencyProvider;
use PhpLightning\Lightning;
use PhpLightningTest\Feature\Fake\FakeHttpApi;
use PHPUnit\Framework\TestCase;

final class LightningFeature extends TestCase
{
    public function test_get_get_callback_url(): void
    {
        $this->bootstrapGacela();
        $this->mockLnPaymentRequest();

        $json = Lightning::getCallbackUrl();

        self::assertEquals([
            'callback' => 'https://callback.url/receiver',
            'maxSendable' => 10_000,
            'minSendable' => 1_000,
            'metadata' => '[["text/plain","Pay to receiver@domain.com"],["text/identifier","receiver@domain.com"]]',
            'tag' => 'payRequest',
            'commentAllowed' => false,
        ], json_decode($json, true));
    }

    public function test_ln_bits_feature(): void
    {
        $this->bootstrapGacela();
        $this->mockLnPaymentRequest();

        $json = Lightning::generateInvoice(amount: 2_000);

        self::assertEquals([
            'pr' => 'lnbc10u1pjzh489...fake payment_request',
            'status' => 'OK',
            'successAction' => [
                'tag' => 'message',
                'message' => 'Payment received!',
            ],
            'routes' => [],
            'disposable' => false,
            'reason' => '',
        ], json_decode($json, true));
    }

    private function bootstrapGacela(): void
    {
        Gacela::bootstrap(__DIR__, static function (GacelaConfig $config): void {
            $config->resetInMemoryCache();
            $config->addAppConfigKeyValues(
                (new LightningConfig())
                    ->setCallbackUrl('https://callback.url/receiver')
                    ->setDomain('domain.com')
                    ->setReceiver('receiver')
                    ->setSendableRange(1_000, 10_000)
                    ->addBackend(
                        (new LnBitsBackendConfig())
                            ->setApiEndpoint('http://localhost:5000')
                            ->setApiKey('XYZ'),
                    )->jsonSerialize(),
            );
        });
    }

    private function mockLnPaymentRequest(): void
    {
        AnonymousGlobal::overrideExistingResolvedClass(
            InvoiceDependencyProvider::class,
            new class() extends AbstractDependencyProvider {
                public function provideModuleDependencies(Container $container): void
                {
                    $container->set(InvoiceDependencyProvider::HTTP_API, static fn () => new FakeHttpApi());
                }
            },
        );
    }
}
