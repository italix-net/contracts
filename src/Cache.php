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
 * Italix Contracts - Cache
 *
 * @package Italix\Contracts
 * @license Apache-2.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

/**
 * Somewhere to keep an answer that was expensive to work out.
 *
 * ## Why this is here and not only in `italix/cache`
 *
 * Because a second library wanted to talk to a cache — `italix/orm`, which can
 * skip a query it has already answered — and the alternative was for an ORM to
 * depend on a caching library, or a caching library to know about queries.
 * Neither is true of either. What they share is this shape, so this is where it
 * goes; the same move {@see KeyValueStore} records for the rate limiter.
 *
 * `Italix\Cache\Cache` extends this and adds `clear()`, which is an
 * administrative act rather than part of the seam: a consumer that caches its
 * own answers has no business emptying everybody's cache.
 *
 * ## What an implementation must never do
 *
 * **Treat a miss as an error.** Every read returns the default rather than
 * throwing. A cache that can fail a request has made the application slower
 * *and* less reliable, which is the opposite of the entire point — so an
 * unreachable backing store behaves as a permanent miss.
 */
interface Cache
{
    /**
     * The stored value, or `$default` when absent or expired.
     *
     * @param  mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Store a value.
     *
     * `$ttl_n = 0` means **no expiry** — `set()` with two arguments has to mean
     * something useful, and "store this and discard it at once" is not it. A
     * negative TTL means already expired: the entry goes if it was there, and
     * the call reports success, because "this key does not resolve" is the
     * post-condition the caller wanted.
     *
     * An implementation physically unable to store without an expiry must
     * **throw** rather than invent a lifetime. A value that vanishes at an hour
     * nobody chose is worse than a call that fails where it is written.
     *
     * @param mixed $value
     * @param int   $ttl_n seconds; 0 = no expiry, negative = already expired
     */
    public function set(string $key, $value, int $ttl_n = 0): bool;

    public function has(string $key): bool;

    public function delete(string $key): bool;

    /**
     * The cached value, or the result of `$producer`, stored and returned.
     *
     * The method that actually gets used: the get-check-set dance is the same
     * three lines every time, and writing them by hand is how one of the three
     * ends up missing.
     *
     * @param  callable():mixed $producer
     * @return mixed
     */
    public function remember(string $key, int $ttl_n, callable $producer);
}
