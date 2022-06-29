<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Exceptions;

final class MixedParametersType extends InvalidArgumentException
{
    public function __construct(string $message = null)
    {
        parent::__construct($message ?? 'You cannot mix different types of parameters.');
    }
}
