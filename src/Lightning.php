<?php

declare(strict_types=1);

namespace PhpLightning;

use PhpLightning\LnAddress\LnAddressFacade;

final class Lightning
{
    public static function generateInvoice(int $amount, string $backend): array
    {
        return (new LnAddressFacade())
            ->generateInvoice($amount, $backend);
    }
}
