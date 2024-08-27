<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\Account\ValueObject;

use App\SharedKernel\Domain\ValueObject\PercentVO;
use InvalidArgumentException;

class FeePercent extends PercentVO
{
    public function __construct(protected float $value)
    {
        if ($value < 0 || $value > 100) {
            throw new InvalidArgumentException('Fee percent must be between 0 and 100');
        }
        parent::__construct($value);
    }
}
