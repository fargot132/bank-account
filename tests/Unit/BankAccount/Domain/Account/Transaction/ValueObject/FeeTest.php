<?php

declare(strict_types=1);

namespace App\Tests\Unit\BankAccount\Domain\Account\Transaction\ValueObject;

use App\BankAccount\Domain\Account\Exception\TransactionFeeException;
use App\BankAccount\Domain\Account\Transaction\ValueObject\Fee;
use PHPUnit\Framework\TestCase;

class FeeTest extends TestCase
{
    public function testValidAmount(): void
    {
        $value = 1000;
        $fee = new Fee($value);
        $this->assertEquals($value, $fee->value());

        $value = 0;
        $fee = new Fee($value);
        $this->assertEquals($value, $fee->value());
    }

    public function testInvalidAmount(): void
    {
        $this->expectException(TransactionFeeException::class);
        new Fee(-1);
    }

    public function testToFloat(): void
    {
        $value = 1000;
        $fee = new Fee($value);
        $this->assertEquals($value / 100, $fee->toFloat());
    }

    public function testIsGreaterOrEqual(): void
    {
        $fee1 = new Fee(1000);
        $fee2 = new Fee(1000);
        $this->assertTrue($fee1->isGreaterOrEqual($fee2));

        $fee1 = new Fee(1000);
        $fee2 = new Fee(1001);
        $this->assertFalse($fee1->isGreaterOrEqual($fee2));

        $fee1 = new Fee(1001);
        $fee2 = new Fee(1000);
        $this->assertTrue($fee1->isGreaterOrEqual($fee2));
    }

    public function testEquality(): void
    {
        $value = 1000;
        $fee1 = new Fee($value);
        $fee2 = new Fee($value);
        $this->assertTrue($fee1->equals($fee2));
    }

    public function testAdd(): void
    {
        $fee1 = new Fee(1000);
        $fee2 = new Fee(1000);
        $fee3 = $fee1->add($fee2);
        $this->assertEquals(2000, $fee3->value());
    }

    public function testSubstract(): void
    {
        $fee1 = new Fee(1010);
        $fee2 = new Fee(1000);
        $fee3 = $fee1->substract($fee2);
        $this->assertEquals(10, $fee3->value());
    }

    public function testMultiply(): void
    {
        $fee1 = new Fee(1000);
        $fee2 = $fee1->multiply(2);
        $this->assertEquals(2000, $fee2->value());

        $fee1 = new Fee(1000);
        $fee2 = $fee1->multiply(0.3333333);
        $this->assertEquals(333, $fee2->value());
    }

    public function testJsonSerialization(): void
    {
        $value = 1033;
        $fee = new Fee($value);
        $expectedJson = json_encode($value / 100);
        $this->assertEquals($expectedJson, json_encode($fee));
    }

    public function testStringRepresentation(): void
    {
        $value = 1000;
        $fee = new Fee($value);
        $expectedString = (string)($value / 100);
        $this->assertEquals($expectedString, (string)$fee);
    }
}
