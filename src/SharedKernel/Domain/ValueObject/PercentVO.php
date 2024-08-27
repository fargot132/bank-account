<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObject;

abstract class PercentVO implements \Stringable, \JsonSerializable
{
    public function __construct(protected float $value)
    {
    }

    public function value(): float
    {
        return $this->value;
    }

    public function toFraction(): float
    {
        return $this->value / 100;
    }

    public function equals(PercentVO $other): bool
    {
        return $this->value() === $other->value();
    }

    public function jsonSerialize(): float
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
