<?php

namespace App\SharedKernel\Domain\ValueObject;

enum Currency: string
{
    case EUR = 'EUR';
    case USD = 'USD';
    case GBP = 'GBP';
    case PLN = 'PLN';
}
