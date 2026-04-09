<?php
/**
 * Italix Contracts - DataContainer Class
 *
 * @package Italix\Contracts
 * @license LGPL-3.0
 */

declare(strict_types=1);

namespace Italix\Contracts;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * A lightweight array-backed data container with array access.
 *
 * Provides a clean, object-oriented wrapper around associative arrays
 * with array syntax support, iteration, JSON serialization, and
 * convenience methods for common operations.
 *
 * All public methods use snake_case following Italix conventions.
 * The camelCase methods required by PHP interfaces (ArrayAccess,
 * JsonSerializable, IteratorAggregate) are implemented but have
 * snake_case aliases for consistency.
 *
 * @example
 * $data = new DataContainer(['name' => 'Alice', 'age' => 30]);
 *
 * // Array syntax
 * $data['email'] = 'alice@example.com';
 * echo $data['name'];     // 'Alice'
 * isset($data['email']);   // true
 *
 * // Method syntax
 * $data->set('city', 'Rome');
 * $data->get('city');           // 'Rome'
 * $data->get('missing', 'N/A'); // 'N/A'
 * $data->has('name');           // true
 *
 * // Bulk operations
 * $data->merge(['role' => 'admin', 'active' => true]);
 *
 * // Extraction
 * $data->to_array();              // full array
 * $data->only(['name', 'email']); // subset
 * $data->except(['age']);         // all except
 *
 * // Works with count(), json_encode(), foreach
 * count($data);           // 5
 * json_encode($data);     // {"name":"Alice",...}
 * foreach ($data as $k => $v) { ... }
 */
class DataContainer implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    /** @var array The underlying data */
    protected array $data;

    /**
     * Create a new DataContainer.
     *
     * @param array $data Initial data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    // ============================================
    // ArrayAccess (required by PHP interface)
    // ============================================

    /** @inheritDoc */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /** @inheritDoc */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    /** @inheritDoc */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /** @inheritDoc */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    // ============================================
    // Countable (required by PHP interface)
    // ============================================

    /** @inheritDoc */
    public function count(): int
    {
        return count($this->data);
    }

    // ============================================
    // IteratorAggregate (required by PHP interface)
    // ============================================

    /** @inheritDoc */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Snake_case alias for getIterator().
     *
     * @return Traversable
     */
    public function get_iterator(): Traversable
    {
        return $this->getIterator();
    }

    // ============================================
    // JsonSerializable (required by PHP interface)
    // ============================================

    /** @inheritDoc */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * Snake_case alias for jsonSerialize().
     *
     * @return array
     */
    public function json_serialize(): array
    {
        return $this->data;
    }

    // ============================================
    // Convenience Methods (snake_case API)
    // ============================================

    /**
     * Get a value by key, with an optional default.
     *
     * @param string $key
     * @param mixed $default Value to return if key is missing
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    /**
     * Set a value by key.
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function set(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Check if a key exists and is not null.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Check if a key exists (even if null).
     *
     * @param string $key
     * @return bool
     */
    public function has_key(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Remove a key from the container.
     *
     * @param string $key
     * @return static
     */
    public function remove(string $key): self
    {
        unset($this->data[$key]);
        return $this;
    }

    /**
     * Merge additional data into the container.
     *
     * Existing keys are overwritten by the new values.
     *
     * @param array $data
     * @return static
     */
    public function merge(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    // ============================================
    // Extraction Methods
    // ============================================

    /**
     * Get all data as a plain array.
     *
     * @return array
     */
    public function to_array(): array
    {
        return $this->data;
    }

    /**
     * Get only the specified keys.
     *
     * @param array $keys Keys to include
     * @return array
     */
    public function only(array $keys): array
    {
        return array_intersect_key($this->data, array_flip($keys));
    }

    /**
     * Get all data except the specified keys.
     *
     * @param array $keys Keys to exclude
     * @return array
     */
    public function except(array $keys): array
    {
        return array_diff_key($this->data, array_flip($keys));
    }

    /**
     * Get all keys in the container.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /**
     * Get all values in the container.
     *
     * @return array
     */
    public function values(): array
    {
        return array_values($this->data);
    }

    // ============================================
    // State Methods
    // ============================================

    /**
     * Check if the container is empty.
     *
     * @return bool
     */
    public function is_empty(): bool
    {
        return empty($this->data);
    }

    /**
     * Clear all data from the container.
     *
     * @return static
     */
    public function clear(): self
    {
        $this->data = [];
        return $this;
    }
}
