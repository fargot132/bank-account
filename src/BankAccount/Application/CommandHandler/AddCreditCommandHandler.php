<?php

declare(strict_types=1);

namespace App\BankAccount\Application\CommandHandler;

use App\BankAccount\Application\Command\AddCreditCommand;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Type;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AddCreditCommandHandler extends TransactionCommandHandler
{
    public function __invoke(AddCreditCommand $command): void
    {
        $this->handle($command, Type::CREDIT);
    }
}
