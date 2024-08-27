<?php

declare(strict_types=1);

namespace App\BankAccount\Application\EventHandler;

use App\BankAccount\Domain\Account\Event\DebitTransactionAddedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DebitTransactionAddedEventHandler
{
    public function __invoke(DebitTransactionAddedEvent $event): void
    {
        /**
         * TODO: Implement this method
         */
    }
}
