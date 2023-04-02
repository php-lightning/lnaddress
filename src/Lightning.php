<?php

declare(strict_types=1);

namespace PhpLightning;

use PhpLightning\Invoice\InvoiceFacade;

final class Lightning
{
    public static function generateInvoice(int $amount, string $backend): array
    {
        return (new InvoiceFacade())->generate($amount, $backend);
    }
}
