<?php

declare(strict_types=1);

namespace PhpLightning\Shared\Value;

use Stringable;

use function sprintf;

/**
 * LNURL-pay metadata as defined by LUD-06.
 *
 * @see https://github.com/lnurl/luds/blob/luds/06.md
 */
final readonly class LnurlPayMetadata implements Stringable
{
    public function __construct(
        private string $descriptionTemplate,
        private string $lnAddress,
    ) {
    }

    /**
     * JSON array of tagged metadata entries. Built with json_encode (not string
     * concatenation) so a description/address containing quotes cannot produce
     * invalid JSON. UNESCAPED_SLASHES keeps URLs readable for the paying wallet.
     */
    public function __toString(): string
    {
        return json_encode([
            ['text/plain', $this->description()],
            ['text/identifier', $this->lnAddress],
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
    }

    public function description(): string
    {
        return sprintf($this->descriptionTemplate, $this->lnAddress);
    }
}
