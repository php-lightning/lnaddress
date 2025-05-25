<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\CallbackUrl;

use function sprintf;

final readonly class LnAddressGenerator implements LnAddressGeneratorInterface
{
    public function __construct(
        private string $defaultLnAddress,
        private string $domain,
    ) {
    }

    public function generate(?string $username): string
    {
        if ($username === null || $username === '') {
            return $this->defaultLnAddress;
        }

        return sprintf('%s@%s', $username, $this->domain);
    }
}
