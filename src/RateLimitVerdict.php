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
 * Italix Contracts - RateLimitVerdict
 *
 * @package Italix\Contracts
 * @license Apache-2.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

/**
 * What a rate limiter decided about one attempt.
 *
 * Deliberately two methods. A consumer needs to know whether to refuse, and how
 * long to tell the caller to wait; everything else — the running count, the
 * limit, the window's end — is useful for diagnostics and belongs to the
 * implementation, not to the seam.
 */
interface RateLimitVerdict
{
    public function is_exceeded(): bool;

    /**
     * Seconds until the caller may try again — the value for a `Retry-After`
     * header. Note the `_n` postfix: this is a count of seconds, not a
     * timestamp.
     */
    public function retry_after_n(): int;
}
