<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts;

use IteratorAggregate;
use Traversable;

interface ResultInterface extends IteratorAggregate
{
    /**
     * Returns the next row of the result as an associative array or NULL if there are no more rows.
     *
     * @return array|null
     */
    public function fetch(): ?array;

    /**
     * Returns an array containing all the result rows represented as associative arrays.
     *
     * @return list<array>
     */
    public function fetchAll(): array;

    /**
     * Returns an associative array with the keys mapped to the index column and the values being
     * an associative array representing all the columns and their values.
     *
     * @return array<array>
     */
    public function fetchAllIndexed(): array;

    /**
     * Returns an associative array with the keys mapped to the first column and the values being
     * an associative array representing the rest of the columns and their values.
     *
     * @return array<array>
     */
    public function fetchAllIndexedByFirstColumn(): array;

    /**
     * Returns an array containing all the result rows represented as numeric arrays.
     *
     * @return list<list<mixed>>
     */
    public function fetchAllNumeric(): array;

    /**
     * Returns an array with the keys mapped to the index column and the values being
     * an associative array representing all the columns and their values.
     *
     * @return array<list<mixed>>
     */
    public function fetchAllNumericIndexed(): array;

    /**
     * Returns an array with the keys mapped to the first column and the values being
     * an associative array representing the rest of the columns and their values.
     *
     * @return array<list<mixed>>
     */
    public function fetchAllNumericIndexedByFirstColumn(): array;

    /**
     * Returns an array containing all the result rows represented as objects.
     *
     * @return list<object>
     */
    public function fetchAllObject(): array;

    /**
     * Returns an array with the keys mapped to the index column and the values being
     * an object representing all the columns and their values.
     *
     * @return array<object>
     */
    public function fetchAllObjectIndexed(): array;

    /**
     * Returns an array containing the values of the first column of the result.
     *
     * @return list<mixed>
     */
    public function fetchAllValues(): array;

    /**
     * Returns an associative array with the keys mapped to the first column
     * and the values of the second column of the result.
     */
    public function fetchAllValuesIndexed(): array;

    /**
     * Returns the next row of the result as a numeric array or NULL if there are no more rows.
     *
     * @return list<mixed>|null
     */
    public function fetchNumeric(): ?array;

    /**
     * Returns the next row of the result as an object or NULL if there are no more rows.
     *
     * @return object|null
     */
    public function fetchObject(): ?object;

    /**
     * Returns the first value of the next row of the result or FALSE if there are no more rows.
     */
    public function fetchValue(): mixed;

    public function free(): void;

    public function getColumnCount(): int;

    public function getRowCount(): int;

    /**
     * @return Traversable<int,array>
     */
    public function iterate(): Traversable;

    /**
     * Returns an iterator over the result set with the keys mapped to the first column and the values being
     * an associative array representing all the columns and their values.
     *
     * @return Traversable<array>
     */
    public function iterateIndexed(): Traversable;

    /**
     * Returns an iterator over the result set with the keys mapped to the first column and the values being
     * an associative array representing the rest of the columns and their values.
     *
     * @return Traversable<array>
     */
    public function iterateIndexedByFirstColumn(): Traversable;

    /**
     * @return Traversable<int,list<mixed>>
     */
    public function iterateNumeric(): Traversable;

    /**
     * @return Traversable<list<mixed>>
     */
    public function iterateNumericIndexed(): Traversable;

    /**
     * Returns an iterator over the result set with the keys mapped to the first column and the values being
     * an array representing the rest of the columns and their values.
     *
     * @return Traversable<list<mixed>>
     */
    public function iterateNumericIndexedByFirstColumn(): Traversable;

    /**
     * @return Traversable<int,object>
     */
    public function iterateObject(): Traversable;

    /**
     * @return Traversable<object>
     */
    public function iterateObjectIndexed(): Traversable;

    /**
     * @return Traversable<int,mixed>
     */
    public function iterateValues(): Traversable;

    /**
     * @return Traversable<mixed>
     */
    public function iterateValuesIndexed(): Traversable;

    public function setIndexColumn(int|string|null $column): static;
}
