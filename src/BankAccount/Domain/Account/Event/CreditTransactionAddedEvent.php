<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\Account\Event;

use App\SharedKernel\Domain\DomainEvent;
use App\SharedKernel\Domain\ValueObject\UuidVO;

class CreditTransactionAddedEvent extends DomainEvent
{
    public function __construct(
        UuidVO $id,
        public readonly UuidVO $accountId,
    ) {
        parent::__construct($id);
    }
}
