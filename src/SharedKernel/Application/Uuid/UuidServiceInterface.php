<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Uuid;

interface UuidServiceInterface
{
    public function generate(): string;
    public function validate(string $uuid): bool;
}
