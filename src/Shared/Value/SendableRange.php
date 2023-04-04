<?php

declare(strict_types=1);

namespace PhpLightning\Shared\Value;

final class SendableRange
{
    /** @var int 100 Minimum in msat (sat/1000) */
    private const DEFAULT_MIN_SENDABLE_IN_MILLISATS = 100_000;

    /** @var int 10 000 000 Maximum in msat (sat/1000) */
    private const DEFAULT_MAX_SENDABLE_IN_MILLISATS = 10_000_000_000;

    private function __construct(
        private int $min,
        private int $max,
    ) {
    }

    public static function default(): self
    {
        return self::withMinMax(
            self::DEFAULT_MIN_SENDABLE_IN_MILLISATS,
            self::DEFAULT_MAX_SENDABLE_IN_MILLISATS,
        );
    }

    public static function withMinMax(int $min, int $max): self
    {
        return new self($min, $max);
    }

    public function contains(int $amount): bool
    {
        return $amount >= $this->min
            && $amount <= $this->max;
    }

    public function min(): int
    {
        return $this->min;
    }

    public function max(): int
    {
        return $this->max;
    }
}
