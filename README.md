# Italix Contracts

Shared interfaces and foundational classes for Italix libraries, enabling seamless integration between `italix/orm`, `italix/forms`, and other compatible libraries.

## Installation

```bash
composer require italix/contracts
```

## Purpose

This package provides the common contracts and building blocks that allow different Italix libraries to work together without tight coupling:

- **italix/orm** implements these contracts on its `Table` and `Column` classes
- **italix/forms** consumes any object implementing these contracts
- **Third-party libraries** can implement these contracts for compatibility

## Contents

### Interfaces

| Interface | Description |
|-----------|-------------|
| `TableMeta` | Describes a table/entity with its columns |
| `ColumnMeta` | Describes a column/field with its type and constraints |
| `RelationMeta` | Describes a foreign key relationship |
| `RelationalColumnMeta` | A column that may have FK relations (extends `ColumnMeta`) |
| `PolymorphicColumnMeta` | A polymorphic FK column (extends `ColumnMeta`) |
| `DelegatedTableMeta` | A table using delegated types pattern (extends `TableMeta`) |

### Classes

| Class | Description |
|-------|-------------|
| `DataContainer` | Lightweight array-backed data container with array access, iteration, and JSON support |

## DataContainer

A general-purpose data container that wraps an associative array and provides both array syntax (`$c['key']`) and a clean method-based API.

Implements `ArrayAccess`, `Countable`, `IteratorAggregate`, and `JsonSerializable`.

### Quick Start

```php
use Italix\Contracts\DataContainer;

// Create with initial data
$user = new DataContainer(['name' => 'Alice', 'age' => 30]);

// Array syntax works naturally
$user['email'] = 'alice@example.com';
echo $user['name'];      // 'Alice'
isset($user['email']);    // true
unset($user['age']);

// Method syntax with fluent chaining
$user->set('city', 'Rome')
     ->set('role', 'admin')
     ->merge(['active' => true, 'score' => 100]);
```

### Getting Values

```php
$user = new DataContainer(['name' => 'Alice', 'role' => 'admin']);

// get() with default value — never throws on missing keys
$user->get('name');              // 'Alice'
$user->get('missing');           // null
$user->get('missing', 'N/A');   // 'N/A'

// Check existence
$user->has('name');       // true  — key exists and is not null
$user->has('missing');    // false
$user->has_key('name');   // true  — key exists (even if value is null)
```

### Extracting Data

```php
$user = new DataContainer([
    'id' => 1,
    'name' => 'Alice',
    'email' => 'alice@example.com',
    'password' => 'hashed...',
]);

// Get everything as a plain array
$user->to_array();
// ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com', 'password' => 'hashed...']

// Pick specific keys
$user->only(['name', 'email']);
// ['name' => 'Alice', 'email' => 'alice@example.com']

// Exclude sensitive keys
$user->except(['password']);
// ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com']

// Inspect keys and values
$user->keys();    // ['id', 'name', 'email', 'password']
$user->values();  // [1, 'Alice', 'alice@example.com', 'hashed...']
```

### Modifying Data

```php
$config = new DataContainer(['debug' => false]);

// set() returns $this for chaining
$config->set('debug', true)
       ->set('log_level', 'info');

// merge() overwrites existing keys
$config->merge(['debug' => false, 'cache' => true]);
// Result: ['debug' => false, 'log_level' => 'info', 'cache' => true]

// Remove a key
$config->remove('cache');

// Clear everything
$config->clear();
$config->is_empty(); // true
```

### Iteration, Counting, and JSON

```php
$data = new DataContainer(['a' => 1, 'b' => 2, 'c' => 3]);

// count()
count($data);  // 3

// foreach
foreach ($data as $key => $value) {
    echo "$key: $value\n";
}

// json_encode()
echo json_encode($data);  // {"a":1,"b":2,"c":3}

// Check state
$data->is_empty();  // false
$data->count();     // 3
```

### Extending DataContainer

DataContainer is designed to be extended. The `$data` property is `protected`,
so subclasses can access it directly:

```php
use Italix\Contracts\DataContainer;

class Config extends DataContainer
{
    /**
     * Get a nested value using dot notation.
     */
    public function dot_get(string $path, $default = null)
    {
        $keys = explode('.', $path);
        $value = $this->data;

        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return $default;
            }
            $value = $value[$key];
        }

        return $value;
    }
}

$config = new Config([
    'database' => ['host' => 'localhost', 'port' => 3306],
]);

$config->dot_get('database.host');  // 'localhost'
$config->dot_get('database.name', 'myapp');  // 'myapp' (default)
```

```php
use Italix\Contracts\DataContainer;

class FormData extends DataContainer
{
    /**
     * Sanitize all string values.
     */
    public function sanitize(): self
    {
        foreach ($this->data as $key => $value) {
            if (is_string($value)) {
                $this->data[$key] = trim(strip_tags($value));
            }
        }
        return $this;
    }
}

$input = new FormData($_POST);
$input->sanitize();
$clean_name = $input->get('name');
```

### snake_case Convention

All Italix public methods use `snake_case`. The methods required by PHP's
built-in interfaces (`offsetGet`, `jsonSerialize`, `getIterator`) are
implemented as required, with `snake_case` aliases provided:

| PHP interface method | snake_case alias |
|---------------------|-----------------|
| `jsonSerialize()` | `json_serialize()` |
| `getIterator()` | `get_iterator()` |

The `ArrayAccess` methods (`offsetGet`, `offsetSet`, `offsetExists`, `offsetUnset`)
don't need aliases because they are called implicitly via array syntax (`$c['key']`),
not directly. Use `get()`, `set()`, `has()`, and `remove()` instead.

### Complete API Reference

| Method | Returns | Description |
|--------|---------|-------------|
| `__construct(array $data = [])` | | Create with optional initial data |
| `get($key, $default = null)` | `mixed` | Get value by key, with default |
| `set($key, $value)` | `self` | Set a value (chainable) |
| `has($key)` | `bool` | Key exists and is not null |
| `has_key($key)` | `bool` | Key exists, even if null |
| `remove($key)` | `self` | Remove a key (chainable) |
| `merge(array $data)` | `self` | Merge in new data (chainable) |
| `to_array()` | `array` | Get all data as plain array |
| `only(array $keys)` | `array` | Get subset of keys |
| `except(array $keys)` | `array` | Get all data except specified keys |
| `keys()` | `array` | Get all keys |
| `values()` | `array` | Get all values |
| `is_empty()` | `bool` | Check if container has no data |
| `clear()` | `self` | Remove all data (chainable) |
| `count()` | `int` | Number of entries (also works with `count()`) |
| `json_serialize()` | `array` | Get data for JSON encoding |
| `get_iterator()` | `Traversable` | Get iterator for `foreach` |

## Interfaces

### Implementing TableMeta

```php
use Italix\Contracts\TableMeta;
use Italix\Contracts\ColumnMeta;

class UsersTable implements TableMeta
{
    private array $columns;

    public function describe_columns(): iterable
    {
        return $this->columns;
    }

    public function describe_column(string $name): ?ColumnMeta
    {
        return $this->columns[$name] ?? null;
    }
}
```

### Implementing ColumnMeta

```php
use Italix\Contracts\ColumnMeta;

class VarcharColumn implements ColumnMeta
{
    public function get_name(): string { return $this->name; }
    public function get_type(): string { return 'VARCHAR'; }
    public function is_nullable(): bool { return $this->nullable; }
    public function is_primary_key(): bool { return false; }
    public function get_length(): ?int { return $this->length; }
    public function get_default() { return $this->default; }
    public function has_default(): bool { return $this->hasDefault; }
}
```

### Using with italix/forms

```php
use Italix\Forms\FormMeta;

// Any TableMeta implementation works
$form = new FormMeta($usersTable);

// DelegatedTableMeta enables delegation
$form = new FormMeta($thingsTable);
$form->delegate('Book');  // Merges Thing + Book columns
```

## Hierarchy

```
Interfaces:

    TableMeta
        └── DelegatedTableMeta

    ColumnMeta
        ├── RelationalColumnMeta
        └── PolymorphicColumnMeta

    RelationMeta (standalone)

Classes:

    DataContainer (ArrayAccess, Countable, IteratorAggregate, JsonSerializable)
```

## Requirements

- PHP 7.4 or higher

## License

LGPL-3.0

