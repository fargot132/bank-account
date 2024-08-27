<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\EventBus;

use App\SharedKernel\Application\EventBus\EventBusInterface;
use App\SharedKernel\Domain\DomainEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class EventBus implements EventBusInterface
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }
    public function dispatch(DomainEvent $event): void
    {
        $this->messageBus->dispatch($event);
    }
}
