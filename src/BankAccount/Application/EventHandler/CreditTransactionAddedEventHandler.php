<?php

declare(strict_types=1);

namespace App\BankAccount\Application\EventHandler;

use App\BankAccount\Domain\Account\Event\CreditTransactionAddedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreditTransactionAddedEventHandler
{
    public function __invoke(CreditTransactionAddedEvent $event): void
    {
        /**
         * TODO: Implement this method
         */
    }
}
