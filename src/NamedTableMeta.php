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
 * Italix Contracts - NamedTableMeta Interface
 *
 * @package Italix\Contracts
 * @license Apache-2.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

/**
 * A table descriptor that also knows the name of the table it describes.
 *
 * `TableMeta` deliberately says nothing about storage: a form can be built
 * from a set of columns that never touch a database. Anything that has to
 * write a row, however, needs the table name as well — and getting it by
 * duck-typing (`method_exists($schema, 'get_name')`) is the kind of
 * convention this framework replaces with a type.
 *
 * Hence a second, narrower interface rather than a new method on `TableMeta`:
 * adding a method to an interface every library type-hints against would
 * break every implementation outside this tree, including the ones written by
 * applications. Adding an interface breaks nothing.
 *
 * @example
 * function insert_row(NamedTableMeta $schema, array $row): void
 * {
 *     $sql = 'INSERT INTO ' . $schema->get_name() . ' (…)';
 * }
 */
interface NamedTableMeta extends TableMeta
{
    /**
     * Get the name of the table this descriptor describes.
     */
    public function get_name(): string;
}
