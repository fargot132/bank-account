<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\Account\Exception;

use App\SharedKernel\Domain\DomainException;
use Throwable;

class NegativeTransactionAmountException extends DomainException
{
    protected const string ERROR_MESSAGE = 'Amount must be greater than 0';
}
