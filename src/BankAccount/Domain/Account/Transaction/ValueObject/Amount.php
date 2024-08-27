<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\Account\Transaction\ValueObject;

use App\BankAccount\Domain\Account\Exception\NegativeTransactionAmountException;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

class Amount extends MoneyVO
{
    /**
     * @throws NegativeTransactionAmountException
     */
    public function __construct(protected int $value)
    {
        if ($value <= 0) {
            throw new NegativeTransactionAmountException();
        }
        parent::__construct($this->value);
    }
}
