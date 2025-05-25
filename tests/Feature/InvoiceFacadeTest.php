<?php

declare(strict_types=1);

namespace PhpLightningTest\Feature;

use Gacela\Framework\AbstractProvider;
use Gacela\Framework\Bootstrap\GacelaConfig;
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

        $json = $this->facade->getCallbackUrl('bob');

        self::assertEquals([
            'callback' => 'https://callback.url/receiver',
            'maxSendable' => 10_000,
            'minSendable' => 1_000,
            'metadata' => '[["text/plain","Pay to bob@domain.com"],["text/identifier","bob@domain.com"]]',
            'tag' => 'payRequest',
            'commentAllowed' => false,
        ], $json);
    }

    public function test_ln_bits_feature(): void
    {
        $this->bootstrapGacela();
        $this->mockLnPaymentRequest();

        $json = $this->facade->generateInvoice('alice', 2_000);

        self::assertEquals([
            'bolt11' => 'lnbc10u1p5r9lmwpp53magnx9u5m3f3tnrm36ztj9rfdfhx5ga3zns7mefh2v0svax8uzqcqzyssp54twf429a8cvz6tflw5lt705gfnvuykhdeewey009tugjcuamt38q9q7sqqqqqqqqqqqqqqqqqqqsqqqqqysgqdqqmqz9gxqrrssrzjqwryaup9lh50kkranzgcdnn2fgvx390wgj5jd07rwr3vxeje0glclll4ttz7sp6kpvqqqqlgqqqqqeqqjq0uu89sejjllry5ye43x0v42jn48c6alfc9mfnjla2u6kmwy444pzrjmtu25nk2shshuh2mrqtehygmzya9xg89ppszuuhd9296vvcxspkpwc68',
            'status' => 'pending',
            'successAction' => [
                'tag' => 'message',
                'message' => 'Payment received!',
            ],
            'routes' => [],
            'disposable' => false,
            'memo' => '',
            'error' => null,
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
                    ->addBackendsFile(__DIR__ . DIRECTORY_SEPARATOR . 'nostr.json')
                    ->jsonSerialize(),
            );
        });
    }

    private function mockLnPaymentRequest(): void
    {
        Gacela::overrideExistingResolvedClass(
            InvoiceDependencyProvider::class,
            new class() extends AbstractProvider {
                public function provideModuleDependencies(Container $container): void
                {
                    $container->set(InvoiceDependencyProvider::HTTP_API, static fn () => new FakeHttpApi());
                }
            },
        );
    }
}
