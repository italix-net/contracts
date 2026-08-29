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
 * Italix Contracts - KeyValueStore
 *
 * @package Italix\Contracts
 * @license Apache-2.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

/**
 * Expiring storage for counters and short-lived values.
 *
 * It began in `italix/crypto`, where the rate limiter was its only consumer and
 * an interface with one consumer belongs with that consumer. `italix/cache`
 * wants the identical shape, and that is the moment house rule 6 applies: two
 * libraries needing to talk means the seam moves here.
 *
 * **`increment()` is part of the contract, not something the caller assembles.**
 * A limiter is exactly the case where two concurrent requests must not both
 * read 4 and write 5, so an implementation that cannot increment atomically is
 * a limiter that can be beaten by opening two tabs.
 */
interface KeyValueStore
{
    /**
     * The stored value, or null when absent or expired.
     *
     * @return mixed
     */
    public function get(string $key);

    /**
     * @param mixed $value
     * @param int   $ttl_n seconds; 0 or less means "already expired"
     */
    public function put(string $key, $value, int $ttl_n): void;

    public function forget(string $key): void;

    /**
     * Add one to a counter, creating it with the given lifetime if absent, and
     * return the new value.
     *
     * The lifetime is set **when the counter is created** and not extended by
     * later increments — that is what makes a window fixed rather than sliding
     * forward every time someone knocks.
     */
    public function increment(string $key, int $ttl_n): int;

    /**
     * When the key stops being valid, or null when absent or non-expiring.
     */
    public function expires_t(string $key): ?int;
}
