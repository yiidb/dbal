<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Exceptions;

final class InternalErrorException extends LogicException
{
    public function __construct(string $message)
    {
        parent::__construct("Internal error: $message");
    }
}
