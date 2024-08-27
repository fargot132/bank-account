<?php

declare(strict_types=1);

namespace App\Tests\Integration\BankAccount\Application;

use App\BankAccount\Application\Command\CreateAccountCommand;
use App\BankAccount\Domain\Account\Event\AccountCreatedEvent;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use App\SharedKernel\Application\MessageBus\CommandBusInterface;
use App\SharedKernel\Domain\ValueObject\Currency;
use App\SharedKernel\Infrastructure\EventBus\EventBus;
use App\SharedKernel\Infrastructure\Uuid\UuidService;
use App\Tests\TestCase\IntegrationTestCase;
use DateTime;
use Symfony\Component\Uid\Uuid;

class CreateAccountTest extends IntegrationTestCase
{
    public function testCreateAccount(): void
    {
        $eventBus = $this->createMock(EventBus::class);
        $container = self::getContainer();
        $container->set(EventBusInterface::class, $eventBus);
        $commandBus = $container->get(CommandBusInterface::class);

        $eventBus->expects(self::once())->method('dispatch')->with($this->isInstanceOf(AccountCreatedEvent::class));

        $command = new CreateAccountCommand(
            (new UuidService())->generate(),
            Currency::USD
        );

        $commandBus->command($command);

        $result = $this->connection->fetchAllAssociative(
            'SELECT * FROM account WHERE id = :id',
            ['id' => $command->id],
            ['id' => 'uuid']
        );

        self::assertCount(1, $result);
        self::assertSame($command->id, (string)Uuid::fromBinary($result[0]['id']));
        self::assertSame($command->currency->value, $result[0]['currency']);
        self::assertSame(0, $result[0]['balance_value']);
        self::assertSame(0.5, $result[0]['fee_percent_value']);
        self::assertTrue($this->isValidDateTime($result[0]['created_at']));
        self::assertTrue($this->isValidDateTime($result[0]['updated_at']));
    }

    private function isValidDateTime(string $dateTime, string $format = 'Y-m-d H:i:s'): bool
    {
        $date = DateTime::createFromFormat($format, $dateTime);
        return $date && $date->format($format) === $dateTime;
    }
}
