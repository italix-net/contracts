# Italix Contracts

Shared interfaces (contracts) for Italix libraries, enabling seamless integration between `italix/orm`, `italix/forms`, and other compatible libraries.

## Installation

```bash
composer require italix/contracts
```

## Purpose

This package provides the common interfaces that allow different Italix libraries to work together without tight coupling:

- **italix/orm** implements these contracts on its `Table` and `Column` classes
- **italix/forms** consumes any object implementing these contracts
- **Third-party libraries** can implement these contracts for compatibility

## Interfaces

### Core Interfaces

| Interface | Description |
|-----------|-------------|
| `TableMeta` | Describes a table/entity with its columns |
| `ColumnMeta` | Describes a column/field with its type and constraints |

### Relational Interfaces

| Interface | Description |
|-----------|-------------|
| `RelationMeta` | Describes a foreign key relationship |
| `RelationalColumnMeta` | A column that may have FK relations (extends `ColumnMeta`) |
| `PolymorphicColumnMeta` | A polymorphic FK column (extends `ColumnMeta`) |

### Delegation Interface

| Interface | Description |
|-----------|-------------|
| `DelegatedTableMeta` | A table using delegated types pattern (extends `TableMeta`) |

## Usage

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

## Interface Hierarchy

```
TableMeta
    └── DelegatedTableMeta

ColumnMeta
    ├── RelationalColumnMeta
    └── PolymorphicColumnMeta

RelationMeta (standalone)
```

## Requirements

- PHP 7.4 or higher

## License

LGPL v.3

