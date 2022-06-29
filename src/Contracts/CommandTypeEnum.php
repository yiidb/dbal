<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts;

enum CommandTypeEnum
{
    case Query;
    case Statement;
    case Unknown;
}
