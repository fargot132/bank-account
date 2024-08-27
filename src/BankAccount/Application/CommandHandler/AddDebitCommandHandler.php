<?php

declare(strict_types=1);

namespace App\BankAccount\Application\CommandHandler;

use App\BankAccount\Application\Command\AddDebitCommand;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Type;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AddDebitCommandHandler extends TransactionCommandHandler
{
    public function __invoke(AddDebitCommand $command): void
    {
        $this->handle($command, Type::DEBIT);
    }
}
