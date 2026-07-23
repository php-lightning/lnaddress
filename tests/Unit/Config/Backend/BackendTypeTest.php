<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Config\Backend;

use PhpLightning\Config\Backend\BackendType;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class BackendTypeTest extends TestCase
{
    public function test_from_string_resolves_known_type(): void
    {
        self::assertSame(BackendType::Lnbits, BackendType::fromString('lnbits'));
    }

    public function test_from_string_lists_supported_types_on_unknown(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown backend type "paypal". Supported types: lnbits');

        BackendType::fromString('paypal');
    }
}
