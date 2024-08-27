<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObject;

use JsonSerializable;
use Stringable;

class MoneyVO implements Stringable, JsonSerializable
{
    public function __construct(protected int $value)
    {
    }

    public function value(): int
    {
        return $this->value;
    }

    public function toFloat(): float
    {
        return $this->value / 100;
    }

    public function equals(MoneyVO $other): bool
    {
        return $this->value() === $other->value();
    }

    public function isGreaterOrEqual(MoneyVO $other): bool
    {
        return $this->value() >= $other->value();
    }

    public function add(MoneyVO $other): static
    {
        /** @phpstan-ignore-next-line  */
        return new static($this->value() + $other->value());
    }

    public function substract(MoneyVO $other): static
    {
        /** @phpstan-ignore-next-line  */
        return new static($this->value() - $other->value());
    }

    public function multiply(float $multiplier): static
    {
        /** @phpstan-ignore-next-line  */
        return new static((int)round($this->value() * $multiplier));
    }

    public function jsonSerialize(): float
    {
        return $this->value / 100;
    }

    public static function fromFloat(float $value): static
    {
        /** @phpstan-ignore-next-line  */
        return new static((int)($value * 100));
    }

    public static function fromString(string $value): static
    {
        /** @phpstan-ignore-next-line  */
        return new static((int)$value * 100);
    }

    public function __toString(): string
    {
        return (string)($this->value / 100);
    }
}
