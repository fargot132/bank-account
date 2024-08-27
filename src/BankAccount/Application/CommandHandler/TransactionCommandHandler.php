<?php

declare(strict_types=1);

namespace App\BankAccount\Application\CommandHandler;

use App\BankAccount\Application\Command\TransactionCommand;
use App\BankAccount\Domain\Account\AccountRepositoryInterface;
use App\BankAccount\Domain\Account\Transaction\Transaction;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Amount;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Id as TransactionId;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Type;
use App\BankAccount\Domain\Account\ValueObject\Id;
use App\SharedKernel\Application\EventBus\EventBusInterface;

abstract class TransactionCommandHandler
{
    public function __construct(
        private EventBusInterface $eventBus,
        private AccountRepositoryInterface $accountRepository
    ) {
    }

    protected function handle(TransactionCommand $command, Type $type): void
    {
        $account = $this->accountRepository->get(new Id($command->accountId));
        if ($account === null) {
            throw new \RuntimeException('Account not found');
        }

        $debit = Transaction::create(
            new TransactionId($command->id),
            $type,
            Amount::fromFloat($command->amount),
            $command->currency
        );

        $account->addTransaction($debit);
        $this->accountRepository->save($account);
        foreach ($account->pullEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
