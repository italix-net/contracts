<?php
/**
 * Italix Contracts - DelegatedTableMeta Interface
 *
 * @package Italix\Contracts
 *  LGPL-3.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

/**
 * Interface for tables that use the Delegated Types pattern.
 *
 * Extends TableMeta to add support for type-discriminated hierarchies
 * where a base table delegates to type-specific sub-tables. This pattern
 * is ideal for Schema.org-style hierarchies:
 *
 *     things (id, type, type_path, name, description)
 *       ├── books (id, thing_id, isbn, pages)
 *       │     └── comics_books (id, book_id, illustrator, is_color)
 *       └── movies (id, thing_id, director, duration)
 *
 * This enables form generators to:
 * - Resolve full delegation chains (Thing → Book → ComicsBook)
 * - Merge columns from all levels into a single form
 * - Auto-hide glue columns (type, type_path, foreign keys, PKs)
 * - Support wildcard mode for admin forms with type selectors
 *
 * @example
 * $things_table = mysql_table('things', [...])
 *     ->type_column('type')
 *     ->type_path_column('type_path')
 *     ->delegate_foreign_key('thing_id')
 *     ->delegates([
 *         'Book'  => $books_table,
 *         'Movie' => $movies_table,
 *     ]);
 *
 * // Use with forms
 * $form = new FormMeta($things_table);
 * $form->delegate('Book');  // Shows thing + book fields
 */
interface DelegatedTableMeta extends TableMeta
{
    /**
     * Get the type discriminator column name.
     *
     * This column stores the type identifier (e.g., 'Book', 'Movie').
     *
     * @return string|null E.g., 'type'
     */
    public function get_type_column(): ?string;

    /**
     * Get the type path column name.
     *
     * This column stores the full hierarchy path (e.g., 'Thing/Book/ComicsBook').
     * Return null if type path tracking is not used.
     *
     * @return string|null E.g., 'type_path'
     */
    public function get_type_path_column(): ?string;

    /**
     * Get the foreign key column name used in delegate tables.
     *
     * This is the column in delegate tables that references this table's
     * primary key (e.g., 'thing_id' in the books table).
     *
     * @return string E.g., 'thing_id'
     */
    public function get_delegate_foreign_key(): string;

    /**
     * Get the direct delegate sub-tables.
     *
     * Returns a map of type names to their corresponding tables.
     * Each delegate table may itself implement DelegatedTableMeta
     * to support deeper delegation chains.
     *
     * @return array<string, TableMeta>
     * E.g., ['Book' => $books_table, 'Movie' => $movies_table]
     */
    public function get_delegate_tables(): array;
}
