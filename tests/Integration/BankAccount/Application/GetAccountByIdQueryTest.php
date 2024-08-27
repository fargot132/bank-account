<?php

declare(strict_types=1);

namespace App\Tests\Integration\BankAccount\Application;

use App\BankAccount\Application\Command\CreateAccountCommand;
use App\BankAccount\Application\Query\GetAccountByIdQuery;
use App\SharedKernel\Application\MessageBus\CommandBusInterface;
use App\SharedKernel\Application\MessageBus\QueryBusInterface;
use App\SharedKernel\Domain\ValueObject\Currency;
use App\SharedKernel\Infrastructure\Uuid\UuidService;
use App\Tests\TestCase\IntegrationTestCase;
use DateTime;

class GetAccountByIdQueryTest extends IntegrationTestCase
{
    public function testGetAccountByIdQuery(): void
    {
        $container = self::getContainer();
        $commandBus = $container->get(CommandBusInterface::class);
        $queryBus = $container->get(QueryBusInterface::class);

        $command = new CreateAccountCommand(
            (new UuidService())->generate(),
            Currency::USD
        );

        $commandBus->command($command);

        $result = $queryBus->query(
            new GetAccountByIdQuery($command->id)
        );

        self::assertSame($command->id, $result->id);
        self::assertSame($command->currency->value, $result->currency);
        self::assertSame(0.0, $result->balance);
        self::assertSame(0.5, $result->feePercent);
        self::assertTrue($this->isValidDateTime($result->createdAt));
        self::assertTrue($this->isValidDateTime($result->updatedAt));
    }

    private function isValidDateTime(string $dateTime, string $format = 'Y-m-d H:i:s'): bool
    {
        $date = DateTime::createFromFormat($format, $dateTime);
        return $date && $date->format($format) === $dateTime;
    }
}
