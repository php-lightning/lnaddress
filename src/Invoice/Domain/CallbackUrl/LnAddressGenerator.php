<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\CallbackUrl;

final class LnAddressGenerator implements LnAddressGeneratorInterface
{
    public function __construct(
        private string $defaultLnAddress,
        private string $domain,
    ) {
    }

    public function generate(?string $username): string
    {
        if (empty($username)) {
            return $this->defaultLnAddress;
        }

        return sprintf('%s@%s', $username, $this->domain);
    }
}
