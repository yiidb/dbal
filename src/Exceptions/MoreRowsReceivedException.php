<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Exceptions;

final class MoreRowsReceivedException extends LogicException
{
    public function __construct()
    {
        parent::__construct('One row expected. More rows received.');
    }
}
