<?php

declare(strict_types=1);

namespace PhpLightning;

use Closure;
use Gacela\Framework\Bootstrap\GacelaConfig;
use PhpLightning\Config\LightningConfig;
use PhpLightning\Invoice\InvoiceFacade;

final class Lightning
{
    public const CONFIG_FILE = 'lightning-config.dist.php';

    private const CONFIG_LOCAL_FILE = 'lightning-config.php';

    public static function getCallbackUrl(): string
    {
        $invoice = (new InvoiceFacade())->getCallbackUrl();

        return json_encode($invoice, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    public static function generateInvoice(int $amount, string $backend = 'lnbits'): string
    {
        $invoice = (new InvoiceFacade())->generateInvoice($amount, $backend);

        return json_encode($invoice, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return Closure(GacelaConfig):void
     */
    public static function configFn(?LightningConfig $lightningConfig = null): Closure
    {
        return static function (GacelaConfig $config) use ($lightningConfig): void {
            if ($lightningConfig !== null) {
                /** @psalm-suppress MixedArgumentTypeCoercion */
                $config->addAppConfigKeyValues($lightningConfig->jsonSerialize());
            }
            $config->addAppConfig(self::CONFIG_FILE, self::CONFIG_LOCAL_FILE);
            $config->setFileCacheEnabled(true);
            $config->setFileCacheDirectory('data/.cache');
        };
    }
}
