<?php

declare(strict_types=1);

namespace App\BankAccount\Infrastructure\Persistence;

use App\BankAccount\Domain\Account\Account;
use App\BankAccount\Domain\Account\AccountRepositoryInterface;
use App\BankAccount\Domain\Account\ValueObject\Id;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Account>
 */
class AccountRepository extends ServiceEntityRepository implements AccountRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }
    public function save(Account $account): void
    {
        $em = $this->getEntityManager();
        $em->persist($account);
        $em->flush();
    }

    public function get(Id $id): ?Account
    {
        return $this->find($id);
    }
}
