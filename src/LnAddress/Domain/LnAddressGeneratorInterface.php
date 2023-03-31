<?php

declare(strict_types=1);

namespace PhpLightning\LnAddress\Domain;

interface LnAddressGeneratorInterface
{
    public function generateLnAddress(): string;
}
