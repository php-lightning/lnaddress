<?php

declare(strict_types=1);

namespace PhpLightningTest\Feature;

use Gacela\Framework\AbstractDependencyProvider;
use Gacela\Framework\Bootstrap\GacelaConfig;
use Gacela\Framework\ClassResolver\GlobalInstance\AnonymousGlobal;
use Gacela\Framework\Container\Container;
use Gacela\Framework\Gacela;
use PhpLightning\Config\LightningConfig;
use PhpLightning\Invoice\InvoiceDependencyProvider;
use PhpLightning\Invoice\InvoiceFacade;
use PhpLightningTest\Feature\Fake\FakeHttpApi;
use PHPUnit\Framework\TestCase;

final class InvoiceFacadeTest extends TestCase
{
    private InvoiceFacade $facade;

    protected function setUp(): void
    {
        $this->facade = new InvoiceFacade();
    }

    public function test_get_get_callback_url(): void
    {
        $this->bootstrapGacela();
        $this->mockLnPaymentRequest();

        $json = $this->facade->getCallbackUrl('username');

        self::assertEquals([
            'callback' => 'https://callback.url/receiver',
            'maxSendable' => 10_000,
            'minSendable' => 1_000,
            'metadata' => '[["text/plain","Pay to username@domain.com"],["text/identifier","username@domain.com"]]',
            'tag' => 'payRequest',
            'commentAllowed' => false,
        ], $json);
    }

    public function test_ln_bits_feature(): void
    {
        $this->bootstrapGacela();
        $this->mockLnPaymentRequest();

        $json = $this->facade->generateInvoice('username', 2_000, 'lnbits');

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
        ], $json);
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
                    ->addBackendsAsJson(__DIR__ . DIRECTORY_SEPARATOR . 'nostr.json')
                    ->jsonSerialize(),
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
