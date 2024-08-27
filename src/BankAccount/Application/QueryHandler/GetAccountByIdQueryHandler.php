<?php

declare(strict_types=1);

namespace App\BankAccount\Application\QueryHandler;

use App\BankAccount\Application\AccountReadRepositoryInterface;
use App\BankAccount\Application\Dto\AccountReadDto;
use App\BankAccount\Application\Query\GetAccountByIdQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetAccountByIdQueryHandler
{
    public function __construct(
        private AccountReadRepositoryInterface $accountReadRepository
    ) {
    }

    public function __invoke(GetAccountByIdQuery $query): AccountReadDto
    {
        return $this->accountReadRepository->getAccountById($query->id);
    }
}
