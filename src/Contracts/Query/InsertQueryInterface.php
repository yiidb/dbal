<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query;

use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Query\TableName;

interface InsertQueryInterface extends BaseQueryInterface
{
    /**
     * @return $this
     */
    public function addRow(array $row): static;

    /**
     * return $this
     */
    public function columns(string ...$columns): static;

    public function execute(): int|string;

    /**
     * @param array[] $rows
     * @return $this
     */
    public function rows(array $rows): static;

    /**
     * @return $this
     */
    public function setValue(string|int $column, mixed $value): static;

    /**
     * @return $this
     */
    public function table(string|TableName|ExpressionInterface $table): static;

    /**
     * @return $this
     */
    public function values(array $values): static;
}
