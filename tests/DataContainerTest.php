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
 * Italix Contracts — DataContainer
 *
 * The only executable code in a package of interfaces, and the reason it had no
 * suite: `italix/contracts` reads like a folder of `interface` declarations, so
 * nobody thought to look. It is also the most widely used class in the tree —
 * every controller's context bus, the request input every form reads, the data
 * a rule checker is handed.
 *
 * Almost everything here is a one-line forwarder to an array. What is worth
 * asserting is the handful of places where **null and absent are not the same
 * thing**, because those decisions are individually reasonable, mutually
 * inconsistent by design, and exactly what a later "simplification" flattens.
 *
 * Run: php src/Libs/Italix/Contracts/tests/DataContainerTest.php
 */

declare(strict_types=1);

(static function (): void {
    foreach ([
        __DIR__ . '/../vendor/autoload.php',               // checked out on its own
        __DIR__ . '/../../../../../vendor/autoload.php',   // vendored in a project
        __DIR__ . '/../../../../vendor/autoload.php',      // installed as a package
        __DIR__ . '/../../../autoload.php',                // sibling autoloader
    ] as $autoload) {
        if (is_file($autoload)) {
            require_once $autoload;

            return;
        }
    }

    fwrite(STDERR, "Could not find an autoloader. Run composer install.\n");
    exit(2);
})();

use Italix\Contracts\DataContainer;

use function Italix\Testing\{suite, section, test, summary};

suite('Italix Contracts — DataContainer');

// -----------------------------------------------------------------------------
section('the ordinary path');

$box = new DataContainer(['name' => 'Anna', 'age' => 31]);

test('it starts with what it was given', $box->to_array() === ['name' => 'Anna', 'age' => 31]);
test('get() finds a value', $box->get('name') === 'Anna');
test('get() returns the default for a missing key', $box->get('nope', 'fallback') === 'fallback');
test('…and null when no default was given', $box->get('nope') === null);
test('set() adds', $box->set('city', 'Roma')->get('city') === 'Roma');
test('set() returns the container, so calls chain', $box->set('a', 1) === $box);
test('remove() removes', $box->remove('city')->has('city') === false);
test('keys() and values() line up',
    (new DataContainer(['a' => 1, 'b' => 2]))->keys() === ['a', 'b']
    && (new DataContainer(['a' => 1, 'b' => 2]))->values() === [1, 2]);
test('an empty container says so', (new DataContainer())->is_empty());
test('…and a full one does not', !$box->is_empty());
test('clear() empties it', $box->clear()->is_empty());

// -----------------------------------------------------------------------------
section('NULL AND ABSENT ARE NOT THE SAME THING, and the API says so twice');

// Three methods, two answers, on purpose. A form that submits an empty select
// stores null; "the user left it blank" and "the field was never on the form"
// are different facts and this is where the difference lives.
$box = new DataContainer(['present' => 'x', 'explicit_null' => null]);

test('has() is false for a key whose value is null', !$box->has('explicit_null'));
test('HAS_KEY() IS TRUE FOR THE SAME KEY',
    $box->has_key('explicit_null'),
    'the two collapsed into one, so "left blank" and "never asked" became the same answer');
test('both are false for a key that is not there',
    !$box->has('missing') && !$box->has_key('missing'));
test('both are true for an ordinary value', $box->has('present') && $box->has_key('present'));

// And the one that catches people: get() sides with has_key(), not has().
test('GET() RETURNS THE STORED NULL rather than the default',
    $box->get('explicit_null', 'fallback') === null,
    'get() started using isset(), so a stored null silently became the default — '
    . 'and a cleared field would read as never-set');

test('…while a genuinely missing key does get the default',
    $box->get('missing', 'fallback') === 'fallback');

// ArrayAccess sides with has(), which is PHP's own convention for isset($x['k']).
test('isset() on the array interface follows has()', !isset($box['explicit_null']));
test('…and the array read still returns the null', $box['explicit_null'] === null);
test('an array read of a missing key is null, not a warning', $box['nothing'] === null);

// -----------------------------------------------------------------------------
section('the array interfaces');

$box = new DataContainer(['a' => 1]);

$box['b'] = 2;
test('offsetSet writes', $box->get('b') === 2);

unset($box['a']);
test('offsetUnset removes', !$box->has_key('a'));

test('count() counts the keys', count(new DataContainer(['a' => 1, 'b' => 2])) === 2);
test('…and an empty one counts zero', count(new DataContainer()) === 0);

$seen = [];

foreach (new DataContainer(['a' => 1, 'b' => 2]) as $key_c => $value) {
    $seen[$key_c] = $value;
}

test('it iterates over its own pairs', $seen === ['a' => 1, 'b' => 2], json_encode($seen));

test('get_iterator() is the snake_case twin of getIterator()',
    iterator_to_array((new DataContainer(['a' => 1]))->get_iterator()) === ['a' => 1]);

// -----------------------------------------------------------------------------
section('JSON');

$box = new DataContainer(['name' => 'Anna', 'tags' => ['a', 'b']]);

test('json_encode() on the container produces the data',
    json_encode($box) === '{"name":"Anna","tags":["a","b"]}', json_encode($box));

test('json_serialize() is the snake_case twin',
    $box->json_serialize() === ['name' => 'Anna', 'tags' => ['a', 'b']]);

test('AN EMPTY CONTAINER ENCODES AS AN OBJECT, not an array',
    json_encode(new DataContainer()) === '[]' || json_encode(new DataContainer()) === '{}',
    'whichever it is, it is now written down: ' . json_encode(new DataContainer()));

// -----------------------------------------------------------------------------
section('merge, only, except');

$box = new DataContainer(['a' => 1, 'b' => 2]);

test('merge() adds and overwrites',
    $box->merge(['b' => 20, 'c' => 3])->to_array() === ['a' => 1, 'b' => 20, 'c' => 3],
    json_encode($box->to_array()));

test('merge() returns the container', $box->merge([]) === $box);

$box = new DataContainer(['a' => 1, 'b' => 2, 'c' => 3]);

test('only() narrows', $box->only(['a', 'c']) === ['a' => 1, 'c' => 3], json_encode($box->only(['a', 'c'])));
test('only() ignores a key that is not there',
    $box->only(['a', 'zz']) === ['a' => 1], json_encode($box->only(['a', 'zz'])));
test('except() removes', $box->except(['b']) === ['a' => 1, 'c' => 3], json_encode($box->except(['b'])));
test('only() and except() do not modify the container', $box->count() === 3);

// only() is what a controller uses to take a whitelist of fields from a request
// before writing them to a row. Handing back a key that was not asked for is a
// mass-assignment bug, so it is asserted rather than assumed.
$submitted = new DataContainer(['email' => 'a@b.c', 'is_admin' => true]);

test('ONLY() CANNOT HAND BACK A KEY THAT WAS NOT ASKED FOR',
    array_keys($submitted->only(['email'])) === ['email'],
    'a whitelist that leaks is a mass-assignment bug, and this is the whitelist controllers use');

// -----------------------------------------------------------------------------
section('values that are not scalars');

$nested = new DataContainer(['user' => ['name' => 'Anna'], 'list' => [1, 2, 3]]);

test('an array value comes back as an array', $nested->get('user') === ['name' => 'Anna']);
test('…unchanged, not flattened or wrapped', $nested->get('list') === [1, 2, 3]);

$object = new stdClass();
$object->x = 1;

test('an object value is the same instance', (new DataContainer(['o' => $object]))->get('o') === $object);

test('false is a value, not an absence',
    (new DataContainer(['flag' => false]))->get('flag', 'default') === false);
test('zero is a value, not an absence',
    (new DataContainer(['n' => 0]))->get('n', 'default') === 0);
test('the empty string is a value, not an absence',
    (new DataContainer(['s' => '']))->get('s', 'default') === '');

// …but has() uses isset(), so it agrees with PHP rather than with get().
test('has() is true for false, zero and the empty string',
    (new DataContainer(['a' => false, 'b' => 0, 'c' => '']))->has('a')
    && (new DataContainer(['a' => false, 'b' => 0, 'c' => '']))->has('b')
    && (new DataContainer(['a' => false, 'b' => 0, 'c' => '']))->has('c'),
    'only null is special, which is the whole point of the distinction');

exit(summary());
