<?php

declare(strict_types=1);

namespace PhpLightning\LnAddress\Domain;

final class FileBaseNameLnAddressGenerator implements LnAddressGeneratorInterface
{
    public function __construct(
        private string $httpHost,
    ) {
    }

    public function generateLnAddress(): string
    {
        // automatically define the ln address based on filename & host, this shouldn't be changed
        $username = str_replace('.php', '', basename(__FILE__));

        return $username . '@' . $this->httpHost;
    }
}
