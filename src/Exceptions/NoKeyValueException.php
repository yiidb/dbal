<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Exceptions;

final class NoKeyValueException extends LogicException
{
    public function __construct(int $columnCount)
    {
        parent::__construct(sprintf(
            'Fetching as key-value pairs requires the result to contain at least 2 columns, %d given.',
            $columnCount
        ));
    }
}
