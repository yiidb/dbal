<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Exceptions;

final class UnknownReference extends LogicException
{
    /**
     * @param string[]|null $knownRefs
     */
    public function __construct(string $tableRef, array $knownRefs = null)
    {
        $prefix = sprintf('The given reference "%s" is not part of any FROM or JOIN clause table.', $tableRef);

        if (empty($knownRefs)) {
            parent::__construct(sprintf('%s No registered references.', $prefix));
        } else {
            $refs = implode(', ', $knownRefs);
            parent::__construct(sprintf('%s The currently registered references are: %s.', $prefix, $refs));
        }
    }
}
