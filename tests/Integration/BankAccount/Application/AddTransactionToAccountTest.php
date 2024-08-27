<?php

declare(strict_types=1);

namespace App\Tests\Integration\BankAccount\Application;

use App\BankAccount\Application\Command\AddCreditCommand;
use App\BankAccount\Application\Command\AddDebitCommand;
use App\BankAccount\Application\Command\CreateAccountCommand;
use App\BankAccount\Domain\Account\Event\AccountCreatedEvent;
use App\BankAccount\Domain\Account\Event\CreditTransactionAddedEvent;
use App\BankAccount\Domain\Account\Event\DebitTransactionAddedEvent;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Type;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use App\SharedKernel\Application\MessageBus\CommandBusInterface;
use App\SharedKernel\Domain\ValueObject\Currency;
use App\SharedKernel\Infrastructure\EventBus\EventBus;
use App\SharedKernel\Infrastructure\Uuid\UuidService;
use App\Tests\TestCase\IntegrationTestCase;
use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class AddTransactionToAccountTest extends IntegrationTestCase
{
    private MockObject $eventBus;

    private CommandBusInterface $commandBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventBus = $this->createMock(EventBus::class);
        $container = self::getContainer();
        $container->set(EventBusInterface::class, $this->eventBus);
        $this->commandBus = $container->get(CommandBusInterface::class);
    }

    public function testAddCreditTransaction(): void
    {
        $matcher = $this->exactly(2);
        $this->eventBus
            ->expects($matcher)
            ->method('dispatch')
            ->willReturnCallback(function ($event) use ($matcher) {
                /** @phpstan-ignore-next-line  */
                match ($matcher->getInvocationCount()) {
                    1 => self::assertInstanceOf(AccountCreatedEvent::class, $event),
                    2 => self::assertInstanceOf(CreditTransactionAddedEvent::class, $event),
                };
            });

        $createAccountCommand = new CreateAccountCommand(
            (new UuidService())->generate(),
            Currency::USD
        );

        $this->commandBus->command($createAccountCommand);

        $transactionCommand = new AddCreditCommand(
            (new UuidService())->generate(),
            $createAccountCommand->id,
            10000,
            Currency::USD
        );

        $this->commandBus->command($transactionCommand);

        $result = $this->connection->fetchAllAssociative(
            'SELECT * FROM account_transaction WHERE id = :id',
            ['id' => $transactionCommand->id],
            ['id' => 'uuid']
        );

        self::assertCount(1, $result);
        self::assertSame($transactionCommand->accountId, (string)Uuid::fromBinary($result[0]['account_id']));
        self::assertSame(Type::CREDIT->value, $result[0]['type']);
        self::assertSame((int)($transactionCommand->amount * 100), $result[0]['amount_value']);
        self::assertSame($transactionCommand->currency->value, $result[0]['currency']);
        self::assertSame(0, $result[0]['fee_value']);
        self::assertTrue($this->isValidDateTime($result[0]['created_at']));
    }

    public function testAddDebitTransaction(): void
    {
        $matcher = $this->exactly(3);
        $this->eventBus
            ->expects($matcher)
            ->method('dispatch')
            ->willReturnCallback(function ($event) use ($matcher) {
                /** @phpstan-ignore-next-line  */
                match ($matcher->getInvocationCount()) {
                    1 => self::assertInstanceOf(AccountCreatedEvent::class, $event),
                    2 => self::assertInstanceOf(CreditTransactionAddedEvent::class, $event),
                    3 => self::assertInstanceOf(DebitTransactionAddedEvent::class, $event),
                };
            });

        $createAccountCommand = new CreateAccountCommand(
            (new UuidService())->generate(),
            Currency::USD,
            2
        );

        $this->commandBus->command($createAccountCommand);

        $transactionCreditCommand = new AddCreditCommand(
            (new UuidService())->generate(),
            $createAccountCommand->id,
            10000,
            Currency::USD
        );

        $this->commandBus->command($transactionCreditCommand);

        $transactionDebitCommand = new AddDebitCommand(
            (new UuidService())->generate(),
            $createAccountCommand->id,
            5000,
            Currency::USD
        );

        $this->commandBus->command($transactionDebitCommand);

        $result = $this->connection->fetchAllAssociative(
            'SELECT * FROM account_transaction WHERE id = :id',
            ['id' => $transactionDebitCommand->id],
            ['id' => 'uuid']
        );

        $amount = (int)($transactionDebitCommand->amount * 100);

        self::assertCount(1, $result);
        self::assertSame($transactionDebitCommand->accountId, (string)Uuid::fromBinary($result[0]['account_id']));
        self::assertSame(Type::DEBIT->value, $result[0]['type']);
        self::assertSame($amount, $result[0]['amount_value']);
        self::assertSame($transactionDebitCommand->currency->value, $result[0]['currency']);
        self::assertSame((int)($amount * $createAccountCommand->feePercent / 100), $result[0]['fee_value']);
        self::assertTrue($this->isValidDateTime($result[0]['created_at']));
    }

    private function isValidDateTime(string $dateTime, string $format = 'Y-m-d H:i:s'): bool
    {
        $date = DateTime::createFromFormat($format, $dateTime);
        return $date && $date->format($format) === $dateTime;
    }
}
