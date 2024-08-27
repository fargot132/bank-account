<?php

declare(strict_types=1);

namespace App\BankAccount\Application\Dto;

readonly class AccountReadDto
{
    public function __construct(
        public string $id,
        public string $currency,
        public float $balance,
        public float $feePercent,
        public string $createdAt,
        public string $updatedAt
    ) {
    }
}
