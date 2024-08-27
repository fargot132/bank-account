<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Uuid;

use App\SharedKernel\Application\Uuid\UuidServiceInterface;
use Symfony\Component\Uid\Uuid;

class UuidService implements UuidServiceInterface
{
    public function generate(): string
    {
        return (string)Uuid::v4();
    }

    public function validate(string $uuid): bool
    {
        return Uuid::isValid($uuid);
    }
}
