<?php

declare(strict_types=1);

namespace App\Tests\Unit\BankAccount\Application\CommandHandler;

use App\BankAccount\Application\Command\CreateAccountCommand;
use App\BankAccount\Application\CommandHandler\CreateAccountCommandHandler;
use App\BankAccount\Domain\Account\Account;
use App\BankAccount\Domain\Account\AccountRepositoryInterface;
use App\BankAccount\Domain\Account\Event\AccountCreatedEvent;
use App\BankAccount\Domain\Account\ValueObject\FeePercent;
use App\BankAccount\Domain\Account\ValueObject\Id;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use App\SharedKernel\Domain\ValueObject\Currency;
use App\SharedKernel\Infrastructure\Uuid\UuidService;
use PHPUnit\Framework\TestCase;

class CreateAccountCommandHandlerTest extends TestCase
{
    public function testInvoke(): void
    {
        $eventBus = $this->createMock(EventBusInterface::class);
        $accountRepository = $this->createMock(AccountRepositoryInterface::class);

        $commandHandler = new CreateAccountCommandHandler($eventBus, $accountRepository);

        $command = new CreateAccountCommand(
            (new UuidService())->generate(),
            Currency::USD
        );

        $accountRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (Account $account) use ($command) {
                    $reflection = new \ReflectionClass($account);

                    $property = $reflection->getProperty('id');
                    $this->assertEquals(new Id($command->id), $property->getValue($account));

                    $property = $reflection->getProperty('currency');
                    $this->assertEquals($command->currency, $property->getValue($account));

                    $property = $reflection->getProperty('feePercent');
                    $this->assertEquals(new FeePercent(0.5), $property->getValue($account));

                    return true;
                })
            );

        $eventBus->expects($this->exactly(1))
            ->method('dispatch')
            ->with($this->isInstanceOf(AccountCreatedEvent::class));

        $commandHandler->__invoke($command);
    }

    public function testInvokeWithFeePercent(): void
    {
        $eventBus = $this->createMock(EventBusInterface::class);
        $accountRepository = $this->createMock(AccountRepositoryInterface::class);

        $commandHandler = new CreateAccountCommandHandler($eventBus, $accountRepository);

        $command = new CreateAccountCommand(
            (new UuidService())->generate(),
            Currency::USD,
            1.5
        );

        $accountRepository->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (Account $account) use ($command) {
                    $reflection = new \ReflectionClass($account);

                    $property = $reflection->getProperty('feePercent');
                    $this->assertEquals(new FeePercent($command->feePercent), $property->getValue($account));

                    return true;
                })
            );

        $commandHandler->__invoke($command);
    }
}
