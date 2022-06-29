<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query;

use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Query\TableName;

interface DeleteQueryInterface extends BaseQueryInterface, QueryWithWhereInterface
{
    public function execute(): int|string;

    /**
     * @return $this
     */
    public function table(string|TableName|ExpressionInterface $table): static;
}
