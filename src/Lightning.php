<?php

declare(strict_types=1);

namespace PhpLightning;

use Closure;
use Gacela\Framework\Bootstrap\GacelaConfig;
use PhpLightning\Invoice\InvoiceFacade;

final class Lightning
{
    public const CONFIG_FILE = 'lightning-config.php';

    private const CONFIG_LOCAL_FILE = 'lightning-config-local.php';

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
    public static function configFn(): Closure
    {
        return static function (GacelaConfig $config): void {
            $config->addAppConfig(self::CONFIG_FILE, self::CONFIG_LOCAL_FILE);
            $config->setFileCacheEnabled(true);
            $config->setFileCacheDirectory('data/.cache');
        };
    }
}
