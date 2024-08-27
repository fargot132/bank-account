<?php

declare(strict_types=1);

namespace App\Tests\Unit\BankAccount\Domain\Account\Transaction;

use App\BankAccount\Domain\Account\Account;
use App\BankAccount\Domain\Account\Transaction\Transaction;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Amount;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Fee;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Id;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Type;
use App\BankAccount\Domain\Account\ValueObject\FeePercent;
use App\SharedKernel\Domain\ValueObject\Currency;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class TransactionTest extends TestCase
{
    public function testCreate(): void
    {
        $id = new Id('123e4567-e89b-12d3-a456-426614174000');
        $type = Type::CREDIT;
        $amount = new Amount(1000);
        $currency = Currency::EUR;

        $transaction = Transaction::create($id, $type, $amount, $currency);

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals($id, $transaction->getId());
        $this->assertEquals($type, $transaction->getType());
        $this->assertEquals($amount, $transaction->getTotalAmount());
        $this->assertEquals($currency, $transaction->getCurrency());
        $this->assertInstanceOf(DateTimeImmutable::class, $transaction->getDate());

        $reflection = new ReflectionClass($transaction);
        $property = $reflection->getProperty('fee');
        $this->assertEquals(0, $property->getValue($transaction)->value());
    }

    public function testSetAccount(): void
    {
        $id = new Id('123e4567-e89b-12d3-a456-426614174000');
        $type = Type::CREDIT;
        $amount = new Amount(1000);
        $currency = Currency::EUR;
        $transaction = Transaction::create($id, $type, $amount, $currency);

        $account = $this->createMock(Account::class);
        $transaction->setAccount($account);

        $reflection = new ReflectionClass($transaction);
        $property = $reflection->getProperty('account');

        $this->assertSame($account, $property->getValue($transaction));
    }

    public function testCalculateFeeAndGetTotalAmount(): void
    {
        $id = new Id('123e4567-e89b-12d3-a456-426614174000');
        $type = Type::CREDIT;
        $amount = new Amount(1000);
        $currency = Currency::USD;
        $transaction = Transaction::create($id, $type, $amount, $currency);

        $feePercent = new FeePercent(0.5);
        $transaction->calculateFee($feePercent);

        $expectedFee = new Fee(5);
        $reflection = new ReflectionClass($transaction);
        $property = $reflection->getProperty('fee');
        $this->assertEquals($expectedFee, $property->getValue($transaction));

        $expectedTotalAmount = new Amount(1005);
        $this->assertEquals($expectedTotalAmount, $transaction->getTotalAmount());
    }
}
