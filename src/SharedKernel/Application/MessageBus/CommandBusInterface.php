<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\MessageBus;

interface CommandBusInterface
{
    public function command(object $command): mixed;
}
