<?php
/**
 * Italix Contracts - PolymorphicColumnMeta Interface
 *
 * @package Italix\Contracts
 *  LGPL-3.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

/**
 * Interface for polymorphic relationship columns.
 *
 * Used for the ID column of polymorphic relationships where the target
 * table varies based on a type discriminator column. For example:
 *
 * - comments.commentable_id + comments.commentable_type
 * - taggables.taggable_id + taggables.taggable_type
 *
 * This enables form generators to create dependent field pairs where
 * the ID field's options change based on the type field's selection.
 *
 * @example
 * // commentable_id column that can reference posts or videos
 * class CommentableIdColumn implements PolymorphicColumnMeta
 * {
 *     // ... ColumnMeta methods ...
 *
 *     public function get_polymorphic_type_column(): string
 *     {
 *         return 'commentable_type';
 *     }
 *
 *     public function get_polymorphic_targets(): array
 *     {
 *         return [
 *             'post'  => $posts_table,
 *             'video' => $videos_table,
 *         ];
 *     }
 * }
 */
interface PolymorphicColumnMeta extends ColumnMeta
{
    /**
     * Get the name of the type discriminator column.
     *
     * @return string E.g., for 'commentable_id', return 'commentable_type'
     */
    public function get_polymorphic_type_column(): string;

    /**
     * Get the available polymorphic targets.
     *
     * Returns a map of type identifiers to their corresponding tables.
     *
     * @return array<string, TableMeta>
     * E.g., ['post' => $posts_table, 'video' => $videos_table]
     */
    public function get_polymorphic_targets(): array;
}
