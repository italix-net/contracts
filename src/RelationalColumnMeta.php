<?php
/**
 * Italix Contracts - RelationalColumnMeta Interface
 *
 * @package Italix\Contracts
 *  LGPL-3.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

/**
 * Interface for columns that may have foreign key relationships.
 *
 * Extends ColumnMeta to add support for describing foreign key relations.
 * Columns implementing this interface can provide RelationMeta to enable
 * automatic select/autocomplete population in forms.
 *
 * @example
 * class CountryIdColumn implements RelationalColumnMeta
 * {
 *     // ... ColumnMeta methods ...
 *
 *     public function get_relation(): ?RelationMeta
 *     {
 *         return new CountryRelation();
 *     }
 * }
 */
interface RelationalColumnMeta extends ColumnMeta
{
    /**
     * Get foreign key relation metadata, if this column is a foreign key.
     *
     * Return null if this column is not a foreign key.
     *
     * @return RelationMeta|null
     */
    public function get_relation(): ?RelationMeta;
}
