<?php

declare(strict_types=1);

namespace PhpLightning\Config\Backend;

use RuntimeException;

use function array_map;
use function implode;
use function sprintf;

enum BackendType: string
{
    case Lnbits = 'lnbits';

    public static function fromString(string $type): self
    {
        return self::tryFrom($type)
            ?? throw new RuntimeException(sprintf(
                'Unknown backend type "%s". Supported types: %s',
                $type,
                implode(', ', array_map(static fn (self $t): string => $t->value, self::cases())),
            ));
    }
}
