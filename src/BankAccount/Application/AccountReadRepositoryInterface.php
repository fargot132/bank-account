<?php

declare(strict_types=1);

namespace App\BankAccount\Application;

use App\BankAccount\Application\Dto\AccountReadDto;

interface AccountReadRepositoryInterface
{
    public function getAccountById(string $id): AccountReadDto;
}
