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
 * Italix Contracts - RuleMeta Interface
 *
 * @package Italix\Contracts
 * @license Apache-2.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

/**
 * Interface for a single declared validation rule.
 *
 * A rule is pure data: it says *what* must hold ("this value is an IBAN"),
 * never *how* to check it and never whether it currently holds. Executing it
 * is somebody else's job — see italix/rules for the reference engine.
 *
 * This contract exists so that a library which merely *carries* rules (a form
 * builder, a table schema, an API endpoint description) does not have to
 * depend on the library that *executes* them, and vice versa. Same role
 * TableMeta plays between italix/orm and italix/forms.
 *
 * Implementations are expected to be immutable value objects.
 *
 * @example
 * class NotEmptyRule implements RuleMeta
 * {
 *     public function get_name(): string  { return 'required'; }
 *     public function get_params(): array { return []; }
 *     public function get_message(): ?string { return null; }
 *     public function to_array(): array
 *     {
 *         return ['rule' => 'required', 'params' => [], 'message' => null];
 *     }
 * }
 */
interface RuleMeta
{
    /**
     * The rule identifier, e.g. 'required', 'max_length', 'iban'.
     *
     * Engines dispatch on this name, so it is the stable part of the contract.
     *
     * @return string
     */
    public function get_name(): string;

    /**
     * Named parameters for the rule, e.g. ['length' => 255].
     *
     * @return array
     */
    public function get_params(): array;

    /**
     * An explicit override message, or null to let the consumer choose one.
     *
     * Prefer returning null and letting the application map get_name() to a
     * translated string: a message baked in here cannot be localised.
     *
     * @return string|null
     */
    public function get_message(): ?string;

    /**
     * Export as a plain array — for JSON transport to client-side validators.
     *
     * @return array
     */
    public function to_array(): array;
}
