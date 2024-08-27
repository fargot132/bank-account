<?php

declare(strict_types=1);

namespace App\BankAccount\Application\Command;

use App\SharedKernel\Domain\ValueObject\Currency;

readonly class CreateAccountCommand
{
    public ?float $feePercent;
    public function __construct(
        public string $id,
        public Currency $currency,
        ?float $feePercent = null
    ) {
        $this->feePercent = $feePercent;
    }
}
