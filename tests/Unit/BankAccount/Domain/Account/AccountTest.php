<?php

declare(strict_types=1);

namespace App\Tests\Unit\BankAccount\Domain\Account;

use App\BankAccount\Domain\Account\Account;
use App\BankAccount\Domain\Account\Event\AccountCreatedEvent;
use App\BankAccount\Domain\Account\Event\CreditTransactionAddedEvent;
use App\BankAccount\Domain\Account\Event\DebitTransactionAddedEvent;
use App\BankAccount\Domain\Account\Exception\CurrencyMismatchException;
use App\BankAccount\Domain\Account\Exception\InsufficientFundsException;
use App\BankAccount\Domain\Account\Exception\TooManyDebitTransactionsException;
use App\BankAccount\Domain\Account\Transaction\Transaction;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Amount;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Type;
use App\BankAccount\Domain\Account\ValueObject\Balance;
use App\BankAccount\Domain\Account\ValueObject\FeePercent;
use App\BankAccount\Domain\Account\ValueObject\Id;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Id as TransactionId;
use App\SharedKernel\Domain\ValueObject\Currency;
use App\SharedKernel\Infrastructure\Uuid\UuidService;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AccountTest extends TestCase
{
    public function testCreate(): void
    {
        $data = [
            'id' => (new UuidService())->generate(),
            'currency' => Currency::EUR,
            'feePercent' => new FeePercent(2),
            'balance' => new Balance(0),
        ];

        $account = Account::create(new Id($data['id']), $data['currency'], $data['feePercent']);
        $this->assertInstanceOf(Account::class, $account);

        $reflection = new ReflectionClass($account);
        foreach ($data as $key => $value) {
            $property = $reflection->getProperty($key);
            $this->assertEquals($value, $property->getValue($account));
        }

        $property = $reflection->getProperty('createdAt');
        $this->assertInstanceOf(DateTimeImmutable::class, $property->getValue($account));

        $property = $reflection->getProperty('updatedAt');
        $this->assertInstanceOf(DateTimeImmutable::class, $property->getValue($account));

        $property = $reflection->getProperty('transactions');
        $this->assertInstanceOf(ArrayCollection::class, $property->getValue($account));
        $this->assertCount(0, $property->getValue($account));

        $events = $account->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(AccountCreatedEvent::class, $events[0]);

        $account = Account::create(new Id($data['id']), $data['currency']);

        $this->assertInstanceOf(Account::class, $account);
        $reflection = new ReflectionClass($account);
        $property = $reflection->getProperty('feePercent');
        $this->assertEquals(new FeePercent(0.5), $property->getValue($account));
    }

    public function testAddCreditTransaction(): void
    {
        $data = [
            'id' => (new UuidService())->generate(),
            'currency' => Currency::EUR,
        ];

        $account = Account::create(new Id($data['id']), $data['currency']);
        $account->pullEvents();

        $transaction = Transaction::create(
            new TransactionId((new UuidService())->generate()),
            Type::CREDIT,
            new Amount(1000),
            $data['currency']
        );

        $account->addTransaction($transaction);

        $reflection = new ReflectionClass($account);
        $property = $reflection->getProperty('transactions');
        $this->assertCount(1, $property->getValue($account));

        $property = $reflection->getProperty('balance');
        $this->assertEquals(new Balance(1000), $property->getValue($account));

        $updatedAt = $reflection->getProperty('updatedAt');
        $createdAt = $reflection->getProperty('createdAt');
        $this->assertGreaterThan($createdAt->getValue($account), $updatedAt->getValue($account));

        $events = $account->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(CreditTransactionAddedEvent::class, $events[0]);
    }

    public function testAddDebitTransaction(): void
    {
        $currency = Currency::EUR;

        $uuidService = new UuidService();

        $account = Account::create(new Id($uuidService->generate()), $currency);
        $account->pullEvents();

        $transaction = Transaction::create(
            new TransactionId($uuidService->generate()),
            Type::CREDIT,
            new Amount(10000),
            $currency
        );

        $account->addTransaction($transaction);
        $account->pullEvents();

        $transaction = Transaction::create(
            new TransactionId($uuidService->generate()),
            Type::DEBIT,
            new Amount(1000),
            $currency
        );

        $account->addTransaction($transaction);

        $reflection = new ReflectionClass($account);
        $property = $reflection->getProperty('transactions');
        $this->assertCount(2, $property->getValue($account));

        $property = $reflection->getProperty('balance');
        $this->assertEquals(new Balance(8995), $property->getValue($account));

        $events = $account->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(DebitTransactionAddedEvent::class, $events[0]);
    }

    public function testCurrencyMismatchException(): void
    {
        $this->expectException(CurrencyMismatchException::class);

        $uuidService = new UuidService();

        $account = Account::create(new Id($uuidService->generate()), Currency::EUR);
        $transaction = Transaction::create(
            new TransactionId($uuidService->generate()),
            Type::CREDIT,
            new Amount(10000),
            Currency::USD
        );

        $account->addTransaction($transaction);
    }

    public function testInsufficientFundsException(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $currency = Currency::EUR;

        $uuidService = new UuidService();

        $account = Account::create(new Id($uuidService->generate()), $currency);

        $transaction = Transaction::create(
            new TransactionId($uuidService->generate()),
            Type::CREDIT,
            new Amount(1000),
            $currency
        );

        $account->addTransaction($transaction);

        $transaction = Transaction::create(
            new TransactionId($uuidService->generate()),
            Type::DEBIT,
            new Amount(1000),
            $currency
        );

        $account->addTransaction($transaction);
    }

    public function testTooManyDebitTransactionsException(): void
    {
        $this->expectException(TooManyDebitTransactionsException::class);

        $currency = Currency::EUR;

        $uuidService = new UuidService();

        $account = Account::create(new Id($uuidService->generate()), $currency);

        $transaction = Transaction::create(
            new TransactionId($uuidService->generate()),
            Type::CREDIT,
            new Amount(100000),
            $currency
        );

        $account->addTransaction($transaction);

        for ($i = 0; $i < 4; $i++) {
            $debitTransaction = Transaction::create(
                new TransactionId($uuidService->generate()),
                Type::DEBIT,
                new Amount(1000),
                $currency
            );
            $account->addTransaction($debitTransaction);
        }
    }
}
