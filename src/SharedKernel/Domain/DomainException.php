<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

use Exception;
use Throwable;

class DomainException extends Exception
{
    protected const string ERROR_MESSAGE = 'Domain error';
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = static::ERROR_MESSAGE;
        }
        parent::__construct($message, $code, $previous);
    }
}
