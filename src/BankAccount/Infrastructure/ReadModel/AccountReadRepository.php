<?php

declare(strict_types=1);

namespace App\BankAccount\Infrastructure\ReadModel;

use App\BankAccount\Application\AccountReadRepositoryInterface;
use App\BankAccount\Application\Dto\AccountReadDto;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class AccountReadRepository implements AccountReadRepositoryInterface
{
    private Connection $connection;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->connection = $entityManager->getConnection();
    }

    /**
     * @throws Exception
     */
    public function getAccountById(string $id): AccountReadDto
    {
        $result = $this->connection->fetchAssociative(
            'SELECT * FROM account WHERE id = :id',
            ['id' => $id],
            ['id' => 'uuid']
        );

        return new AccountReadDto(
            (string)Uuid::fromBinary($result['id']),
            $result['currency'],
            $result['balance_value'] / 100,
            $result['fee_percent_value'],
            $result['created_at'],
            $result['updated_at']
        );
    }
}
