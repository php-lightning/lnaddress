<?php

declare(strict_types=1);

namespace PhpLightningTest\Unit\Shared\Value;

use PhpLightning\Shared\Value\LnurlPayMetadata;
use PHPUnit\Framework\TestCase;

final class LnurlPayMetadataTest extends TestCase
{
    public function test_builds_metadata_json(): void
    {
        $metadata = new LnurlPayMetadata('Pay to %s', 'bob@domain.com');

        self::assertSame(
            '[["text/plain","Pay to bob@domain.com"],["text/identifier","bob@domain.com"]]',
            (string)$metadata,
        );
    }

    public function test_description_applies_template(): void
    {
        $metadata = new LnurlPayMetadata('Pay to %s', 'bob@domain.com');

        self::assertSame('Pay to bob@domain.com', $metadata->description());
    }

    /**
     * A quote in the description used to break the hand-built JSON string;
     * json_encode escapes it so the output stays parseable per LUD-06.
     */
    public function test_escapes_quotes_to_stay_valid_json(): void
    {
        $metadata = new LnurlPayMetadata('Pay "now" to %s', 'bob@domain.com');

        $decoded = json_decode((string)$metadata, true, flags: JSON_THROW_ON_ERROR);

        self::assertSame([
            ['text/plain', 'Pay "now" to bob@domain.com'],
            ['text/identifier', 'bob@domain.com'],
        ], $decoded);
    }
}
