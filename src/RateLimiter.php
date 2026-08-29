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
 * Italix Contracts - RateLimiter
 *
 * @package Italix\Contracts
 * @license Apache-2.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

/**
 * Something that counts attempts and says when there have been too many.
 *
 * This is the seam house rule 6 prescribes: `italix/auth` needs to refuse a
 * login that has been tried too often, `italix/crypto` knows how to count, and
 * neither may depend on the other. The alternative — every application writing
 * a five-line adapter between two of its own framework's libraries — is exactly
 * the ceremony this package exists to remove.
 *
 * Implementations decide the algorithm. `Italix\Crypto\Limiter` uses a fixed
 * window; a token bucket would satisfy the same contract.
 */
interface RateLimiter
{
    /**
     * Record one attempt against $key and report the result.
     *
     * @param string     $key    identifies who is limited — an account, an address
     * @param int        $max_n  attempts allowed per window
     * @param string|int $window seconds, or an expression such as '15 minutes'
     */
    public function hit(string $key, int $max_n, $window = 60): RateLimitVerdict;

    /**
     * Forget the attempts recorded for $key — call after a success.
     */
    public function reset(string $key): void;
}
