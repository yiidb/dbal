<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Exceptions;

final class EmptyWhereNotAllowedException extends LogicException
{
    public function __construct(string $message = null)
    {
        parent::__construct($message ?? 'Empty where was used, expected non-empty where.');
    }
}
