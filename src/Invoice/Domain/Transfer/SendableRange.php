<?php

declare(strict_types=1);

namespace PhpLightning\Invoice\Domain\Transfer;

final class SendableRange
{
    private function __construct(
        private int $min,
        private int $max,
    ) {
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
