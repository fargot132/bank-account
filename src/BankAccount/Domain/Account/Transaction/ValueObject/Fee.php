<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\Account\Transaction\ValueObject;

use App\BankAccount\Domain\Account\Exception\TransactionFeeException;
use App\SharedKernel\Domain\ValueObject\MoneyVO;

class Fee extends MoneyVO
{
    /**
     * @throws TransactionFeeException
     */
    public function __construct(protected int $value)
    {
        if ($value < 0) {
            throw new TransactionFeeException();
        }
        parent::__construct($this->value);
    }
}
