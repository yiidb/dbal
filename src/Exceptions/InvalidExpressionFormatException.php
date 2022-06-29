<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Exceptions;

use Throwable;

final class InvalidExpressionFormatException extends LogicException
{
    public function __construct(string $message = null, Throwable $previous = null)
    {
        parent::__construct($message ?? 'Invalid expression format.', previous: $previous);
    }
}
