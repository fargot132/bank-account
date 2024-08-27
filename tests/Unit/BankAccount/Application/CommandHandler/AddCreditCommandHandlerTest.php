<?php

declare(strict_types=1);

namespace App\Tests\Unit\BankAccount\Application\CommandHandler;

use App\BankAccount\Application\Command\AddCreditCommand;
use App\BankAccount\Application\CommandHandler\AddCreditCommandHandler;
use App\BankAccount\Domain\Account\Account;
use App\BankAccount\Domain\Account\AccountRepositoryInterface;
use App\BankAccount\Domain\Account\Event\CreditTransactionAddedEvent;
use App\BankAccount\Domain\Account\Transaction\Transaction;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Amount;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Id as TransactionId;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Type;
use App\BankAccount\Domain\Account\ValueObject\Id;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use App\SharedKernel\Domain\ValueObject\Currency;
use App\SharedKernel\Infrastructure\Uuid\UuidService;
use PHPUnit\Framework\TestCase;

class AddCreditCommandHandlerTest extends TestCase
{
    public function testInvoke(): void
    {
        $eventBus = $this->createMock(EventBusInterface::class);
        $accountRepository = $this->createMock(AccountRepositoryInterface::class);
        $account = $this->createMock(Account::class);

        $commandHandler = new AddCreditCommandHandler($eventBus, $accountRepository);

        $uuidService = new UuidService();

        $command = new AddCreditCommand(
            $uuidService->generate(),
            $uuidService->generate(),
            1000,
            Currency::USD
        );

        $accountRepository->expects($this->once())
            ->method('get')
            ->with(new Id($command->accountId))
            ->willReturn($account);

        $account->expects($this->once())
            ->method('addTransaction')
            ->with(
                $this->callback(function (Transaction $transaction) use ($command) {
                    $this->assertEquals(new TransactionId($command->id), $transaction->getId());
                    $this->assertEquals(Type::CREDIT, $transaction->getType());
                    $this->assertEquals($command->currency, $transaction->getCurrency());

                    $reflection = new \ReflectionClass($transaction);
                    $property = $reflection->getProperty('amount');
                    $this->assertEquals(Amount::fromFloat($command->amount), $property->getValue($transaction));

                    return true;
                })
            );

        $account->expects($this->once())
            ->method('pullEvents')
            ->willReturn([$this->createMock(CreditTransactionAddedEvent::class)]);

        $accountRepository->expects($this->once())
            ->method('save')
            ->with($account);

        $eventBus->expects($this->exactly(1))
            ->method('dispatch')
            ->with($this->isInstanceOf(CreditTransactionAddedEvent::class));

        $commandHandler->__invoke($command);
    }

    public function testRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Account not found');

        $eventBus = $this->createMock(EventBusInterface::class);
        $accountRepository = $this->createMock(AccountRepositoryInterface::class);

        $commandHandler = new AddCreditCommandHandler($eventBus, $accountRepository);

        $uuidService = new UuidService();

        $command = new AddCreditCommand(
            $uuidService->generate(),
            $uuidService->generate(),
            1000,
            Currency::USD
        );

        $accountRepository->expects($this->once())
            ->method('get')
            ->with(new Id($command->accountId))
            ->willReturn(null);

        $commandHandler->__invoke($command);
    }
}
