<?php

declare(strict_types=1);

namespace App\Tests\Unit\BankAccount\Domain\Account\ValueObject;

use App\BankAccount\Domain\Account\ValueObject\FeePercent;
use PHPUnit\Framework\TestCase;

class FeePercentTest extends TestCase
{
    public function testValidPercentage(): void
    {
        $percent = 10.33;
        $feePercent = new FeePercent($percent);
        $this->assertEquals($percent, $feePercent->value());
    }

    public function testInvalidPercentage(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new FeePercent(-1);

        $this->expectException(\InvalidArgumentException::class);
        new FeePercent(101);
    }

    public function testToFraction(): void
    {
        $percent = 10.33;
        $feePercent = new FeePercent($percent);
        $this->assertEquals($percent / 100, $feePercent->toFraction());
    }

    public function testEquals(): void
    {
        $percent = 10.33;
        $feePercent1 = new FeePercent($percent);
        $feePercent2 = new FeePercent($percent);
        $this->assertTrue($feePercent1->equals($feePercent2));

        $feePercent3 = new FeePercent(10.34);
        $this->assertFalse($feePercent1->equals($feePercent3));
    }

    public function testJsonSerialization(): void
    {
        $percent = 10.33;
        $feePercent = new FeePercent($percent);
        $this->assertEquals($percent, $feePercent->jsonSerialize());
    }

    public function testStringRepresentation(): void
    {
        $percent = 10.33;
        $feePercent = new FeePercent($percent);
        $this->assertEquals((string)$percent, (string)$feePercent);
    }
}
