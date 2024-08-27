<?php

declare(strict_types=1);

namespace App\Tests\Unit\BankAccount\Domain\Account\Transaction\ValueObject;

use App\BankAccount\Domain\Account\Exception\NegativeTransactionAmountException;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Amount;
use PHPUnit\Framework\TestCase;

class AmountTest extends TestCase
{
    public function testValidAmount(): void
    {
        $value = 1000;
        $amount = new Amount($value);
        $this->assertEquals($value, $amount->value());
    }

    public function testInvalidAmount(): void
    {
        $this->expectException(NegativeTransactionAmountException::class);
        new Amount(0);
    }

    public function testToFloat(): void
    {
        $value = 1000;
        $amount = new Amount($value);
        $this->assertEquals($value / 100, $amount->toFloat());
    }

    public function testIsGreaterOrEqual(): void
    {
        $amount1 = new Amount(1000);
        $amount2 = new Amount(1000);
        $this->assertTrue($amount1->isGreaterOrEqual($amount2));

        $amount1 = new Amount(1000);
        $amount2 = new Amount(1001);
        $this->assertFalse($amount1->isGreaterOrEqual($amount2));

        $amount1 = new Amount(1001);
        $amount2 = new Amount(1000);
        $this->assertTrue($amount1->isGreaterOrEqual($amount2));
    }

    public function testEquality(): void
    {
        $value = 1000;
        $amount1 = new Amount($value);
        $amount2 = new Amount($value);
        $this->assertTrue($amount1->equals($amount2));
    }

    public function testAdd(): void
    {
        $amount1 = new Amount(1000);
        $amount2 = new Amount(1000);
        $amount3 = $amount1->add($amount2);
        $this->assertEquals(2000, $amount3->value());
    }

    public function testSubstract(): void
    {
        $amount1 = new Amount(1010);
        $amount2 = new Amount(1000);
        $amount3 = $amount1->substract($amount2);
        $this->assertEquals(10, $amount3->value());
    }

    public function testMultiply(): void
    {
        $amount1 = new Amount(1000);
        $amount2 = $amount1->multiply(2);
        $this->assertEquals(2000, $amount2->value());

        $amount1 = new Amount(1000);
        $amount2 = $amount1->multiply(0.3333333);
        $this->assertEquals(333, $amount2->value());
    }

    public function testJsonSerialization(): void
    {
        $value = 1033;
        $amount = new Amount($value);
        $expectedJson = json_encode($value / 100);
        $this->assertEquals($expectedJson, json_encode($amount));
    }

    public function testStringRepresentation(): void
    {
        $value = 1000;
        $amount = new Amount($value);
        $expectedString = (string)($value / 100);
        $this->assertEquals($expectedString, (string)$amount);
    }
}
