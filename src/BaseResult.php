<?php

declare(strict_types=1);

namespace YiiDb\DBAL;

use Traversable;
use YiiDb\DBAL\Contracts\ResultInterface;
use YiiDb\DBAL\Exceptions\NoKeyValueException;
use YiiDb\DBAL\Exceptions\NotSupportedException;

use function is_int;

abstract class BaseResult implements ResultInterface
{
    private string|int|null $indexColumn = null;

    public function fetchAllIndexed(): array
    {
        $data = [];

        $indexColumn = $this->indexColumn;

        if ($indexColumn === null) {
            foreach ($this->fetchAll() as $row) {
                /**
                 * @noinspection OffsetOperationsInspection
                 * @psalm-suppress MixedArrayOffset
                 */
                $data[reset($row)] = $row;
            }
        } else {
            foreach ($this->fetchAll() as $row) {
                /** @psalm-suppress MixedArrayOffset */
                $data[$row[$indexColumn]] = $row;
            }
        }

        return $data;
    }

    public function fetchAllIndexedByFirstColumn(): array
    {
        $data = [];

        foreach ($this->fetchAll() as $row) {
            /** @psalm-suppress MixedArrayOffset */
            $data[array_shift($row)] = $row;
        }

        return $data;
    }

    public function fetchAllNumericIndexed(): array
    {
        $data = [];

        $indexColumn = $this->indexColumn;

        if ($indexColumn === null) {
            foreach ($this->fetchAllNumeric() as $row) {
                /**
                 * @noinspection OffsetOperationsInspection
                 * @psalm-suppress MixedArrayOffset
                 */
                $data[reset($row)] = $row;
            }
        } elseif (is_int($indexColumn)) {
            foreach ($this->fetchAllNumeric() as $row) {
                /** @psalm-suppress MixedArrayOffset */
                $data[$row[$indexColumn]] = $row;
            }
        } else {
            throw new NotSupportedException(
                'The "fetchAllNumericIndexed" method requires a numeric or null "indexColumn" value.'
            );
        }

        return $data;
    }

    public function fetchAllNumericIndexedByFirstColumn(): array
    {
        $data = [];

        foreach ($this->fetchAllNumeric() as $row) {
            /** @psalm-suppress MixedArrayOffset */
            $data[array_shift($row)] = $row;
        }

        return $data;
    }

    public function fetchAllObject(): array
    {
        $rows = [];

        foreach ($this->fetchAll() as $row) {
            $rows[] = (object)$row;
        }

        return $rows;
    }

    public function fetchAllObjectIndexed(): array
    {
        $rows = [];

        foreach ($this->fetchAllIndexed() as $key => $row) {
            $rows[$key] = (object)$row;
        }

        return $rows;
    }

    public function fetchAllValuesIndexed(): array
    {
        $this->ensureHasKeyValue();

        $data = [];

        foreach ($this->fetchAllNumeric() as [$key, $value]) {
            /** @psalm-suppress MixedArrayOffset */
            $data[$key] = $value;
        }

        return $data;
    }

    public function fetchObject(): ?object
    {
        $row = $this->fetch();

        return $row === null ? null : (object)$row;
    }

    public function getIterator(): Traversable
    {
        return $this->indexColumn === null
            ? $this->iterate()
            : $this->iterateIndexed();
    }

    public function iterate(): Traversable
    {
        while (($row = $this->fetch()) !== null) {
            yield $row;
        }
    }

    public function iterateIndexed(): Traversable
    {
        $indexColumn = $this->indexColumn;

        if ($indexColumn === null) {
            foreach ($this->iterate() as $row) {
                yield reset($row) => $row;
            }
        } else {
            foreach ($this->iterate() as $row) {
                yield $row[$indexColumn] => $row;
            }
        }
    }

    public function iterateIndexedByFirstColumn(): Traversable
    {
        foreach ($this->iterate() as $row) {
            yield array_shift($row) => $row;
        }
    }

    public function iterateNumeric(): Traversable
    {
        while (($row = $this->fetchNumeric()) !== null) {
            yield $row;
        }
    }

    public function iterateNumericIndexed(): Traversable
    {
        $indexColumn = $this->indexColumn;

        if ($indexColumn === null) {
            foreach ($this->iterateNumeric() as $row) {
                yield reset($row) => $row;
            }
        } elseif (is_int($indexColumn)) {
            foreach ($this->iterateNumeric() as $row) {
                yield $row[$indexColumn] => $row;
            }
        } else {
            throw new NotSupportedException(
                'The "iterateNumericIndexed" method requires a numeric or null "indexColumn" value.'
            );
        }
    }

    public function iterateNumericIndexedByFirstColumn(): Traversable
    {
        foreach ($this->iterateNumeric() as $row) {
            yield array_shift($row) => $row;
        }
    }

    public function iterateObject(): Traversable
    {
        foreach ($this->iterate() as $key => $row) {
            yield $key => (object)$row;
        }
    }

    public function iterateObjectIndexed(): Traversable
    {
        foreach ($this->iterateIndexed() as $key => $row) {
            yield $key => (object)$row;
        }
    }

    public function iterateValues(): Traversable
    {
        while (($value = $this->fetchValue()) !== false) {
            yield $value;
        }
    }

    public function iterateValuesIndexed(): Traversable
    {
        $this->ensureHasKeyValue();

        foreach ($this->iterateNumeric() as [$key, $value]) {
            yield $key => $value;
        }
    }

    /**
     * @return $this
     */
    public function setIndexColumn(int|string|null $column): static
    {
        $this->indexColumn = $column;

        return $this;
    }

    private function ensureHasKeyValue(): void
    {
        $columnCount = $this->getColumnCount();

        if ($columnCount < 2) {
            throw new NoKeyValueException($columnCount);
        }
    }
}
