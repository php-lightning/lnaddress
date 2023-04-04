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
use PhpLightning\Http\Domain\HttpClientInterface;
use PhpLightning\Http\HttpDependencyProvider;
use PhpLightning\Lightning;
use PHPUnit\Framework\TestCase;

final class LightningFeature extends TestCase
{
    public function test_ln_bits_feature(): void
    {
        $this->bootstrapGacela();
        $this->mockLnPaymentRequest();

        $json = Lightning::generateInvoice(amount: 2_000);

        self::assertEquals([
            'pr' => 'lnbc10u1pjzh489...CUSTOM PAYMENT REQUEST',
            'status' => 'OK',
            'successAction' => [
                'tag' => 'message',
                'message' => 'Payment received!',
            ],
            'routes' => [],
            'disposable' => false,
        ], json_decode($json, true));
    }

    private function bootstrapGacela(): void
    {
        Gacela::bootstrap(__DIR__, static function (GacelaConfig $config): void {
            $config->resetInMemoryCache();
            $config->addAppConfigKeyValues(
                (new LightningConfig())
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
            HttpDependencyProvider::class,
            new class() extends AbstractDependencyProvider {
                public function provideModuleDependencies(Container $container): void
                {
                    $container->set(
                        HttpDependencyProvider::HTTP_CLIENT,
                        static fn () => new class() implements HttpClientInterface {
                            public function post(string $url, array $options = []): string
                            {
                                return json_encode([
                                    'payment_request' => 'lnbc10u1pjzh489...CUSTOM PAYMENT REQUEST',
                                ], JSON_THROW_ON_ERROR);
                            }
                        },
                    );
                }
            },
        );
    }
}
