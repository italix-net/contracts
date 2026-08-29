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
 * Italix Contracts - RelationalColumnMeta Interface
 *
 * @package Italix\Contracts
 * @license Apache-2.0
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
