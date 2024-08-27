<?php

declare(strict_types=1);

namespace App\Tests\Unit\BankAccount\Domain\Account\ValueObject;

use PHPUnit\Framework\TestCase;
use App\BankAccount\Domain\Account\ValueObject\Balance;

class BalanceTest extends TestCase
{
    public function testValidBalance(): void
    {
        $amount = 1000;
        $balance = new Balance($amount);
        $this->assertEquals($amount, $balance->value());
    }

    public function testToFloat(): void
    {
        $amount = 1000;
        $balance = new Balance($amount);
        $this->assertEquals($amount / 100, $balance->toFloat());
    }

    public function testIsGreaterOrEqual(): void
    {
        $balance1 = new Balance(1000);
        $balance2 = new Balance(1000);
        $this->assertTrue($balance1->isGreaterOrEqual($balance2));

        $balance1 = new Balance(1000);
        $balance2 = new Balance(1001);
        $this->assertFalse($balance1->isGreaterOrEqual($balance2));

        $balance1 = new Balance(1001);
        $balance2 = new Balance(1000);
        $this->assertTrue($balance1->isGreaterOrEqual($balance2));
    }

    public function testEquality(): void
    {
        $amount = 1000;
        $balance1 = new Balance($amount);
        $balance2 = new Balance($amount);
        $this->assertTrue($balance1->equals($balance2));
    }

    public function testAdd(): void
    {
        $balance1 = new Balance(1000);
        $balance2 = new Balance(1000);
        $balance3 = $balance1->add($balance2);
        $this->assertEquals(2000, $balance3->value());
    }

    public function testSubstract(): void
    {
        $balance1 = new Balance(1010);
        $balance2 = new Balance(1000);
        $balance3 = $balance1->substract($balance2);
        $this->assertEquals(10, $balance3->value());
    }

    public function testMultiply(): void
    {
        $balance1 = new Balance(1000);
        $balance2 = $balance1->multiply(2);
        $this->assertEquals(2000, $balance2->value());

        $balance1 = new Balance(1000);
        $balance2 = $balance1->multiply(0.3333333);
        $this->assertEquals(333, $balance2->value());
    }

    public function testJsonSerialization(): void
    {
        $amount = 1033;
        $balance = new Balance($amount);
        $expectedJson = json_encode($amount / 100);
        $this->assertEquals($expectedJson, json_encode($balance));
    }

    public function testStringRepresentation(): void
    {
        $amount = 1000;
        $balance = new Balance($amount);
        $expectedString = (string)($amount / 100);
        $this->assertEquals($expectedString, (string)$balance);
    }
}
