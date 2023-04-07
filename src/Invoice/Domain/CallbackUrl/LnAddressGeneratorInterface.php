<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\CallbackUrl;

interface LnAddressGeneratorInterface
{
    public function generate(?string $username): string;
}
