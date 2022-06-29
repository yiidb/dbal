<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query;

use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Query\TableName;

interface UpdateQueryInterface extends BaseQueryInterface, QueryWithWhereInterface
{
    public function execute(): int|string;

    /**
     * @return $this
     */
    public function set(string $column, mixed $value): static;

    /**
     * @return $this
     */
    public function setRaw(
        string $column,
        string $rawString,
        array $params = [],
        bool $wrap = true
    ): static;

    /**
     * @return $this
     */
    public function table(string|TableName|ExpressionInterface $table): static;
}
