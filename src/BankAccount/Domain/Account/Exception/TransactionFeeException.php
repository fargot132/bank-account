<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\Account\Exception;

use App\SharedKernel\Domain\DomainException;
use Throwable;

class TransactionFeeException extends DomainException
{
    protected const string ERROR_MESSAGE = 'Fee must be greater or equal to 0';
}
