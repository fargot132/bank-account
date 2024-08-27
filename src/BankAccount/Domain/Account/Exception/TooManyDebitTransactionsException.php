<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\Account\Exception;

use App\SharedKernel\Domain\DomainException;
use Throwable;

class TooManyDebitTransactionsException extends DomainException
{
    protected const string ERROR_MESSAGE = 'TransactionFeeException.php';
}
