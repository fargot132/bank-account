<?php

declare(strict_types=1);

namespace App\BankAccount\Application\CommandHandler;

use App\BankAccount\Application\Command\CreateAccountCommand;
use App\BankAccount\Application\Factory\AccountFactory;
use App\BankAccount\Domain\Account\Account;
use App\BankAccount\Domain\Account\AccountRepositoryInterface;
use App\BankAccount\Domain\Account\ValueObject\FeePercent;
use App\BankAccount\Domain\Account\ValueObject\Id;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateAccountCommandHandler
{
    public function __construct(
        private EventBusInterface $eventBus,
        private AccountRepositoryInterface $accountRepository
    ) {
    }

    public function __invoke(CreateAccountCommand $command): void
    {
        $account = Account::create(
            new Id($command->id),
            $command->currency,
            $command->feePercent === null ? $command->feePercent : new FeePercent($command->feePercent)
        );

        $this->accountRepository->save($account);
        foreach ($account->pullEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
