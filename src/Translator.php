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
 * Italix Contracts - Translator
 *
 * @package Italix\Contracts
 * @license Apache-2.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

/**
 * Something that turns a key into a sentence in some language.
 *
 * The seam house rule 6 prescribes. Until this existed there were **two**
 * translators in the framework that could not see each other — `Italix\Mvc`
 * carried one and `Italix\I18n` another — so a library needing to translate had
 * to pick a side, and an application could not move from one to the other
 * gradually.
 *
 * Deliberately small. It says nothing about where messages come from, what
 * syntax they are written in, or how plural categories are decided; those are
 * the implementation's business and they genuinely differ:
 * `Italix\Mvc\Translator` substitutes `:name` and `Italix\I18n\Translator`
 * speaks ICU MessageFormat.
 *
 * ## Why `in()` is part of the contract
 *
 * Because the requirement it serves is not optional: one request routinely needs
 * two languages — an interface in the operator's, an e-mail in the recipient's.
 * A caller handed a translator must be able to obtain one for another locale
 * **without knowing who implements it**, and without a global setter it would
 * then have to remember to undo.
 *
 * Returning `self` rather than `static` keeps this usable on PHP 7.4, which the
 * libraries still target.
 */
interface Translator
{
    /** The locale this instance answers in, as a BCP-47 tag. */
    public function locale_code(): string;

    /** Whether a key exists, without producing a fallback for it. */
    public function has(string $key_c): bool;

    /**
     * The translation for a key.
     *
     * A missing key returns the key itself rather than throwing: a typo in a
     * catalogue should not turn a working page into a 500.
     *
     * @param array<string, mixed> $params
     */
    public function get(string $key_c, array $params = []): string;

    /**
     * The translation for a key, chosen by a count.
     *
     * @param array<string, mixed> $params
     */
    public function choice(string $key_c, int $count_n, array $params = []): string;

    /**
     * An instance answering in another locale, leaving this one untouched.
     *
     * @return self
     */
    public function in(string $locale_c): self;
}
