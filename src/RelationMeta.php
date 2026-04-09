<?php
/**
 * Italix Contracts - RelationMeta Interface
 *
 * @package Italix\Contracts
 *  LGPL-3.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

/**
 * Interface for foreign key relationship metadata.
 *
 * Describes a foreign key relationship and provides a method to fetch
 * options from the related table. Used by form generators to create
 * select dropdowns or autocomplete fields.
 *
 * @example
 * class CountryRelation implements RelationMeta
 * {
 *     public function get_foreign_table(): string { return 'countries'; }
 *     public function get_foreign_key(): string { return 'id'; }
 *     public function get_foreign_label(): string { return 'name'; }
 *
 *     public function fetch_options(int $max_options = 100): ?array
 *     {
 *         // Return ['US' => 'United States', 'CA' => 'Canada', ...]
 *         // or null if too many rows (use autocomplete instead)
 *     }
 * }
 */
interface RelationMeta
{
    /**
     * Get the foreign table name.
     *
     * @return string E.g., 'countries', 'categories', 'users'
     */
    public function get_foreign_table(): string;

    /**
     * Get the foreign key column name in the target table.
     *
     * @return string E.g., 'id', 'code', 'uuid'
     */
    public function get_foreign_key(): string;

    /**
     * Get the column to use as display label in the foreign table.
     *
     * @return string E.g., 'name', 'title', 'display_name'
     */
    public function get_foreign_label(): string;

    /**
     * Fetch option pairs from the related table.
     *
     * Returns an associative array of [key => label] pairs for use in
     * select dropdowns. If the related table has too many rows, return
     * null to indicate that autocomplete should be used instead.
     *
     * @param int $max_options Maximum number of options to fetch (default: 100)
     * @return array|null [key => label] pairs, or null if too many rows
     */
    public function fetch_options(int $max_options = 100): ?array;
}
