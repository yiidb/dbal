<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions;

final class Operator
{
    final public const EQ = '=';
    final public const GT = '>';
    final public const GTE = '>=';
    final public const IN = 'IN';
    final public const LIKE = 'LIKE';
    final public const LT = '<';
    final public const LTE = '<=';
    final public const NEQ = '<>';
    final public const NOT_IN = 'NOT IN';
    final public const NOT_LIKE = 'NOT LIKE';
}
