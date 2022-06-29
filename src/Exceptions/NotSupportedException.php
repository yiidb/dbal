<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Exceptions;

final class NotSupportedException extends LogicException
{
    public function __construct(string $message = null)
    {
        parent::__construct($message ?? 'Not supported.');
    }
}
