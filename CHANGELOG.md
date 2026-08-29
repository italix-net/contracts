# Changelog — italix/contracts

Format: [Keep a Changelog](https://keepachangelog.com/). Versioning policy: `VERSIONING.md` at the
project root.

## [2.0.0] — 2026-08-28

### Changed — BREAKING

`_c` on function/method names is retired in favor of spelling out what the value actually is —
see `src/Libs/Italix/CONVENTIONS.md`, "`_c` is for variables... only." `_c` stays on variables,
parameters, properties and DB columns; only the method name changed, no behavior:

- `Translator::locale_c()` → `locale_code()` — interface method. Every implementer
  (`italix/mvc`'s `Translator`, `italix/i18n`'s `Translator`) renamed to match in the same round;
  both of those libraries also bump MAJOR.

## [1.7.0] — 2026-08-18

### Added

- **`Cache`** — somewhere to keep an answer that was expensive to work out.

  Added because a second library wanted to talk to a cache: `italix/orm` can now skip a query it has
  already answered. The alternatives were an ORM depending on a caching library, or a caching library
  knowing what a query is — and neither is true of either. House rule 6: two libraries needing to
  talk is the moment the seam moves here. Same move as `KeyValueStore` records for the rate limiter.

  `get` / `set` / `has` / `delete` / `remember`. `clear()` is deliberately **not** in it:
  `Italix\Cache\Cache` keeps that one, because emptying everybody's cache is an administrative act,
  not something a consumer caching its own answers should be able to do.

## [1.6.0] — 2026-08-17

### Added

- **`DataContainer` had no tests**, and it is the only executable code in this package — which is
  precisely why it had none: a folder of `interface` declarations does not invite anyone to look.
  It is also the most widely used class in the tree: every controller's context bus, the request
  input every form reads, the data a rule checker is handed.

  43 assertions. Most of the class is a one-line forwarder to an array; what is worth pinning is the
  handful of places where **null and absent are not the same thing**:

  | | a key holding `null` |
  |---|---|
  | `has()` | false — `isset()` semantics, and PHP's own convention |
  | `has_key()` | **true** — the key was set, to nothing |
  | `get($k, $default)` | returns **null**, siding with `has_key()` |
  | `isset($box[$k])` | false, siding with `has()` |

  Individually reasonable, mutually inconsistent on purpose, and exactly what a later
  "simplification" flattens — at which point "the user left this blank" and "the form never had this
  field" become the same answer.

  Three mutations, each failing its own assertion: `get()` switched to `isset()`, `has_key()`
  collapsed onto `has()`, `only()` no longer filtering — the last one being the whitelist a
  controller uses before writing a request to a row, so a leak there is a mass-assignment bug.

## [1.5.0] — 2026-08-13

### Legal

- **Relicensed: LGPL-3.0-only → Apache-2.0.** No code changed, and this is the only genuine
  relicensing in the framework besides `italix/orm` — the other eighteen packages carried no licence
  at all and were licensed for the first time on the same day.

  It is also the one that mattered most, because **every other Italix library depends on this one**
  and on nothing else. LGPL-3.0 on a package of interface declarations was the wrong instrument: its
  obligations are written around linking against a *library with behaviour*, and `Contracts` has
  none — it is method signatures. The practical effect was to make the seam that exists to keep the
  other libraries independent the most legally awkward file in the set.

  Apache-2.0 is the deliberate choice for the shared vocabulary: permissive, with an explicit patent
  grant, so an implementation of these interfaces carries no obligation back. The libraries that
  *have* behaviour are MPL-2.0, whose copyleft is per file — someone who modifies one of their files
  publishes that file, and nothing else.

  **This direction loosens rather than narrows**: every right LGPL-3.0 granted is still granted, and
  more. So it is recorded as a MINOR, where `italix/orm`'s Apache-2.0 → MPL-2.0 took a MAJOR for
  moving the other way. A consumer who chose this package under LGPL-3.0 keeps everything they had.

- The Apache-2.0 `LICENSE` file and the per-file header were applied in the same pass.

## [1.4.0] — 2026-08

### Added

- **`Translator`** — the seam between a library that needs a sentence and whoever supplies it.

  Until now the framework carried **two** translators that could not see each other:
  `Italix\Mvc\Translator` (dot keys, `:placeholder`) and `Italix\I18n\Translator` (ICU, CLDR
  plurals). A library needing to translate had to pick one, and an application could not move from
  one to the other gradually. Both implement this now, so the move can happen one call site at a
  time.

  Deliberately small: five methods, and nothing about where messages live or what syntax they are
  written in — those genuinely differ between the two implementations.

  `in($locale_c)` is **part of the contract**, not an extra. The requirement it serves is not
  optional: one request routinely needs two languages — an interface in the operator's, an e-mail in
  the recipient's — and a caller must be able to obtain the second without knowing who implements the
  first, and without a global setter it would then have to remember to undo.

## [1.2.0] — 2026-08

### Added

- **`RateLimiter`** and **`RateLimitVerdict`** — the seam between `italix/auth`, which must refuse a
  login that has been tried too often, and `italix/crypto`, which knows how to count. Neither may
  depend on the other (house rule 13), and the alternative — every application writing a five-line
  adapter between two of its own framework's libraries — is the ceremony this package exists to
  remove.

  `RateLimitVerdict` is deliberately two methods. A consumer needs to know whether to refuse and how
  long to say to wait; the running count and the window's end are diagnostics and belong to the
  implementation.

## [1.1.0] — 2026-08

### Added

- **`NamedTableMeta extends TableMeta`** — a table descriptor that also knows its own table name.

  `TableMeta` deliberately says nothing about storage: a form can be built from a set of columns
  that never touch a database. Anything that has to *write* a row needs the name as well.

  A second interface rather than a method on `TableMeta`, and the reasoning is the compatibility
  rule in `VERSIONING.md`: adding a method to an interface every library type-hints against would
  break every implementation outside this tree, including the ones written by applications. Adding
  an interface breaks nothing — which is why this is a MINOR and not a 2.0.0.

  `Italix\Orm\Schema\Table` now declares it. That change is purely declarative: `get_name()` already
  existed with the same signature.

## [1.0.0] — baseline

Versioning starts here. This entry records the state of the library at the time the policy was
adopted, not a release. `italix/orm` already declared `"italix/contracts": "^1.0"`, so this number
was effectively already in use.

### Contents

Interfaces with no behaviour. This package is the **only** seam between Italix libraries (house rule
6): libraries never depend on each other, and the graph stays acyclic. Verified across every
`use Italix\…` statement in the tree — `Forms`, `DataSets`, `Calendars` and `Rules` each depend on
`Contracts` and on nothing else in Italix; `Encode` depends on nothing at all.

- `TableMeta` / `ColumnMeta` — lets `italix/forms` build a form from a schema without depending on
  `italix/orm`.
- `RuleMeta` — lets `FieldMeta` carry rules it does not interpret, and lets `italix/rules` execute
  rules it did not define.
- Data container and array-access contracts.

### Stability

This package carries the strictest compatibility obligation in the framework: **every other library
type-hints against it**. Adding an interface is a MINOR. Adding a method to an existing interface is
a **MAJOR**, because every implementer outside this tree breaks — including implementations written
by applications, which is the whole point of an interface-only seam.

Planned additions, each a MINOR (FRAMEWORK-FUTURE.md):

- `Probe` — `name_c()` and `snapshot()`, so the dev toolbar can collect from any library without any
  library knowing the toolbar exists.
- `Sanitizer` — implemented outside, because `Html::raw()` does not inspect what it is given and a
  whitelist for untrusted rich content is a different job the encoder refuses to fake.
- `KeyValueStore` — shared by `Italix\Cache` and the rate limiter in `Italix\Crypto`.
