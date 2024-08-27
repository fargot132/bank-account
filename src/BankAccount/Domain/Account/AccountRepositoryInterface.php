<?php

declare(strict_types=1);

namespace App\BankAccount\Domain\Account;

use App\BankAccount\Domain\Account\ValueObject\Id;

interface AccountRepositoryInterface
{
    public function save(Account $account): void;

    public function get(Id $id): ?Account;
}
