<?php

declare(strict_types=1);

namespace App\Tests\Unit\BankAccount\Domain\Account\ValueObject;

use App\SharedKernel\Infrastructure\Uuid\UuidService;
use PHPUnit\Framework\TestCase;
use App\BankAccount\Domain\Account\ValueObject\Id;
use InvalidArgumentException;

class IdTest extends TestCase
{
    public function testValidUuid(): void
    {
        $uuid = (new UuidService())->generate();
        $id = new Id($uuid);
        $this->assertInstanceOf(Id::class, $id);
        $this->assertEquals($uuid, $id->value());
    }

    public function testInvalidUuid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Id('invalid-uuid');
    }

    public function testEquality(): void
    {
        $uuid = (new UuidService())->generate();
        $id1 = new Id($uuid);
        $id2 = new Id($uuid);
        $this->assertTrue($id1->equals($id2));
    }

    public function testJsonSerialization(): void
    {
        $uuid = (new UuidService())->generate();
        $id = new Id($uuid);
        $this->assertEquals($uuid, $id->jsonSerialize());
    }

    public function testStringRepresentation(): void
    {
        $uuid = (new UuidService())->generate();
        $id = new Id($uuid);
        $this->assertEquals($uuid, (string)$id);
    }
}
