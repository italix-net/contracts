<?php
/**
 * Italix Contracts - TableMeta Interface
 *
 * @package Italix\Contracts
 *  LGPL-3.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

/**
 * Interface for table/entity metadata.
 *
 * Implement this interface on your table or model class to enable
 * compatibility with italix/forms and other Italix libraries.
 *
 * This is the core contract for describing table structure - any class
 * implementing this interface can be used to generate forms, validate
 * data, or introspect schema information.
 *
 * @example
 * class UsersTable implements TableMeta
 * {
 *     public function describe_columns(): iterable
 *     {
 *         return [
 *             'id' => new IdColumn(),
 *             'name' => new NameColumn(),
 *             'email' => new EmailColumn(),
 *         ];
 *     }
 *
 *     public function describe_column(string $name): ?ColumnMeta
 *     {
 *         return $this->describe_columns()[$name] ?? null;
 *     }
 * }
 */
interface TableMeta
{
    /**
     * Return an iterable of column descriptors.
     *
     * The returned iterable should be keyed by column name and contain
     * objects implementing ColumnMeta (or its extended interfaces).
     *
     * @return iterable<string, ColumnMeta>
     */
    public function describe_columns(): iterable;

    /**
     * Get a specific column descriptor by name.
     *
     * @param string $name The column name
     * @return ColumnMeta|null The column descriptor, or null if not found
     */
    public function describe_column(string $name): ?ColumnMeta;
}
