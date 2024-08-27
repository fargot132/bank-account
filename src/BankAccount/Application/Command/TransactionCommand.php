<?php

declare(strict_types=1);

namespace App\BankAccount\Application\Command;

use App\SharedKernel\Domain\ValueObject\Currency;

abstract readonly class TransactionCommand
{
    public function __construct(
        public string $id,
        public string $accountId,
        public float $amount,
        public Currency $currency
    ) {
    }
}
