<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Exceptions;

use function is_array;

final class InvalidArgumentTypeException extends InvalidArgumentException
{
    /**
     * @param string|string[] $expectedType
     */
    public function __construct(string $argument, string|array $expectedType, mixed $passedValue)
    {
        if (is_array($expectedType)) {
            $expectedType = implode('|', $expectedType);
        }

        parent::__construct(sprintf(
            'Argument "%s" expects type "%s", passed type "%s"',
            $argument,
            $expectedType,
            get_debug_type($passedValue)
        ));
    }
}
