<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

use App\SharedKernel\Domain\ValueObject\UuidVO;
use DateTimeImmutable;

abstract class DomainEvent
{
    public readonly DateTimeImmutable $occurredOn;

    public function __construct(public readonly UuidVO $aggregateId)
    {
        $this->occurredOn = new DateTimeImmutable();
    }
}
