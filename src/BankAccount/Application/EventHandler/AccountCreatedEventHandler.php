<?php

declare(strict_types=1);

namespace App\BankAccount\Application\EventHandler;

use App\BankAccount\Domain\Account\Event\AccountCreatedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AccountCreatedEventHandler
{
    public function __invoke(AccountCreatedEvent $event): void
    {
        /**
         * TODO: Implement this method
         */
    }
}
