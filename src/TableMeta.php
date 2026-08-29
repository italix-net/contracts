<?php
/*
 * Copyright 2026 Italix Sas di Andrea Sivieri e soci
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * Italix Contracts - TableMeta Interface
 *
 * @package Italix\Contracts
 * @license Apache-2.0
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
