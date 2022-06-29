<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query;

enum QueryTypeEnum: string
{
    case Delete = 'delete';
    case Insert = 'insert';
    case Select = 'select';
    case Update = 'update';
}
