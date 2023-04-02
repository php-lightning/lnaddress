<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\LnAddress;

interface LnAddressGeneratorInterface
{
    public function generateLnAddress(): string;
}
