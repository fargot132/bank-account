<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

abstract class AggregateRoot
{
    /** @var DomainEvent[] */
    private array $events = [];

    /**
     * @return DomainEvent[]
     */
    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    protected function raise(DomainEvent $event): void
    {
        $this->events[] = $event;
    }
}
