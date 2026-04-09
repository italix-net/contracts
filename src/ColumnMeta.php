<?php
/**
 * Italix Contracts - ColumnMeta Interface
 *
 * @package Italix\Contracts
 *  LGPL-3.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

/**
 * Interface for column/field metadata.
 *
 * Provides the essential information about a database column or model field
 * needed for form generation, validation, and schema introspection.
 *
 * This interface defines 7 core methods that describe a column's structure:
 * - get_name(): The column identifier
 * - get_type(): The data type (VARCHAR, INTEGER, TEXT, etc.)
 * - is_nullable(): Whether NULL values are allowed
 * - is_primary_key(): Whether this is a primary key
 * - get_length(): String length for VARCHAR/CHAR types
 * - get_default(): The default value
 * - has_default(): Whether a default value is set
 *
 * @example
 * class NameColumn implements ColumnMeta
 * {
 *     public function get_name(): string { return 'name'; }
 *     public function get_type(): string { return 'VARCHAR'; }
 *     public function is_nullable(): bool { return false; }
 *     public function is_primary_key(): bool { return false; }
 *     public function get_length(): ?int { return 255; }
 *     public function get_default() { return null; }
 *     public function has_default(): bool { return false; }
 * }
 */
interface ColumnMeta
{
    /**
     * Get the column name.
     *
     * @return string
     */
    public function get_name(): string;

    /**
     * Get the column data type.
     *
     * Common types: VARCHAR, INTEGER, TEXT, BOOLEAN, DATE, DATETIME,
     * TIME, TIMESTAMP, DECIMAL, NUMERIC, JSON, JSONB, UUID, BLOB, etc.
     *
     * @return string
     */
    public function get_type(): string;

    /**
     * Check if the column allows NULL values.
     *
     * @return bool
     */
    public function is_nullable(): bool;

    /**
     * Check if this column is a primary key.
     *
     * @return bool
     */
    public function is_primary_key(): bool;

    /**
     * Get the column length (for VARCHAR, CHAR, etc.).
     *
     * @return int|null The length, or null if not applicable
     */
    public function get_length(): ?int;

    /**
     * Get the default value.
     *
     * @return mixed The default value, or null if none
     */
    public function get_default();

    /**
     * Check if the column has a default value set.
     *
     * This distinguishes between "default is null" and "no default".
     *
     * @return bool
     */
    public function has_default(): bool;
}
