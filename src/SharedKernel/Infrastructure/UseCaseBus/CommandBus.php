<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\UseCaseBus;

use App\SharedKernel\Application\MessageBus\CommandBusInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class CommandBus implements CommandBusInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    public function command(object $command): mixed
    {
        return $this->handle($command);
    }
}
