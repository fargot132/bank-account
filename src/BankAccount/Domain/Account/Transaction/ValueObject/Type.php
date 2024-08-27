<?php

namespace App\BankAccount\Domain\Account\Transaction\ValueObject;

enum Type: string
{
    case DEBIT = 'DEBIT';
    case CREDIT = 'CREDIT';
}
