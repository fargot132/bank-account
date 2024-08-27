<?php

declare(strict_types=1);

namespace App\BankAccount\Application\Query;

readonly class GetAccountByIdQuery
{
    public function __construct(
        public string $id
    ) {
    }
}
