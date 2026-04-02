## [2.3.0] — 2026-04-02 — Modern PHP Compatibility & Global Constants

This release hardens the framework for PHP 8.2, 8.3, and 8.4 by resolving core deprecation warnings and restoring critical global constants for view templates.

---

### 🛡️ PHP 8.2+ Compatibility

**Modified** `Core/libs/Controller.php`
- **Explicit Property Declaration**: Defined `view`, `template`, `mail`, `model`, and `service` properties to eliminate dynamic property deprecation warnings.

**Modified** `Core/libs/View.php`
- **Dynamic Attribute Support**: Added the `#[AllowDynamicProperties]` attribute. This preserves the framework's core feature of allowing controllers to dynamically assign variables to views (e.g., `$this->view->title = 'Home'`) without triggering modern PHP errors.

---

### 🌍 Global Configuration & Templates

**Modified** `App/config/app.php`
- **Constant Restoration**: Re-introduced `define('URL', ...)` and `define('SITE', ...)` global constants. This fixes the `Undefined constant "URL"` fatal error previously encountered in view headers (`Head.php`).
- **Synchronized Config**: Constants are now automatically synced with the `Config` registry values during bootstrap.

---

## [2.2.0] — 2026-04-02 — Unified Logger & CLI Standardization

This release introduces a sophisticated `Logger` class for cross-environment feedback and standardizes all terminal output for a professional developer experience.

---

### 📝 Unified Logger Class

**New File** `Core/libs/Logger.php`
- **Environment Detection**: Automatically switches between ANSI colored output (CLI) and styled HTML `<span>` tags (Web).
- **Professional Formatting**: Standardized methods for `Info` (Cyan), `Success` (Green), `Warning` (Yellow), and `Error` (Red).
- **Backward Compatibility**: Introduced a native `Log` alias to ensure existing code remains functional.

---

### 🖥️ CLI Output Standardization

**Modified** `Core/libs/Migration.php`
- Replaced all raw `echo` status markers (`[SUCCESS]`, `[ERROR]`, `[INFO]`, `[WARNING]`) with the new `Logger` class.
- Improved visual scanning for model registrations and table synchronizations.

**Modified** `Core/libs/Database.php`
- Updated the `applyMigration` method to use professional colored logging for migration table status, scans, and results.

**Modified** `Core/libs/Helper.php`
- Refactored `LoadConfig()` and `SetConfig()` to provide high-visibility error alerts and success notifications.

---

## [2.1.0] — 2026-04-01 — Database Driver & Migration Refactor

Thin release consolidates the database connection layer to support multiple PDO-compatible drivers (MySQL & SQLite) through a unified `Database` class, and upgrades the Migration Engine to be fully driver-aware.

---

### 🗄️ Multi-Driver Database Support

**Modified** `Core/libs/Database.php`
- Unified connection layer: The `Database` class now dynamically generates DSN strings based on the `DB_TYPE` configuration.
- **SQLite Support**: Automatic directory creation for SQLite databases in `ROOT/DataBase/`.
- **MySQL Support**: Maintains full backward compatibility with existing configurations.
- Improved security: `insert()`, `update()`, and `delete()` now strictly use parameterized bindings with associative array matchers.

**Deleted** `Core/libs/PortableDB.php`
- Legacy SQLite-only database handler removed. All functionality merged into the main `Database` class.

**Modified** `Core/libs/Model.php`
- Removed dependency on `PortableDB`. All models now utilize the unified `Database` connection.

---

### 📂 File Management & Uploader Refactor

**Modified** `Core/libs/Uploader.php`
- **Fluent API**: Refactored the class to be object-oriented and configurable via a fluent API (e.g. `setAllowed()`, `setMaxSize()`).
- **MD5-Based Deduplication**: Automatically detects duplicate content using file hashing to prevent redundant storage.
- **Static Helpers**: Introduced static methods for content-based filename generation (`getHashedName`), existence checks (`exists`), and file deletion (`delete`).
- **Improved Validation**: Clear mapping of PHP upload errors onto human-readable messages.

**Modified** `Core/libs/File.php`
- Added `handleDir()` and `delete_file()` to improve filesystem interactions.

---

### 🚀 Driver-Agnostic Migration Engine

**Modified** `Core/libs/Migration.php`
- **Inheritance**: Now extends the `Database` class to share connection and DSN logic.
- **Dynamic Introspection**: Implemented driver-specific schema detection:
  - **MySQL**: Uses `SHOW TABLES` and `SHOW COLUMNS`.
  - **SQLite**: Uses `sqlite_master` and `PRAGMA table_info` (including unique index detection).
- **Driver-Aware Table Creation**: Correctly handles `AUTO_INCREMENT` (MySQL) vs `AUTOINCREMENT` (SQLite).
- **SQLite Alter Support**: Implemented a "temp-swap" strategy for SQLite to handle complex schema changes (modifying or dropping columns) that are not natively supported by SQLite's `ALTER TABLE`.

---

### 🔧 Configuration

**Modified** `App/config/app.php` & `db.conf`
- Standardized database configuration schema. 
- Switching between MySQL and SQLite is now as simple as changing `DB_TYPE` in your configuration.

---

## [2.0.0] — 2026-03-04 — Major Update

This release is a **major architectural overhaul** of the Vanilla Framework, introducing a Mongoose-style ORM, an automated migration engine, a Services layer, security hardening across the entire HTTP pipeline, and a modernized View rendering system.

---

### 🏗️ New Architecture: Mongoose-like ORM

**Added** `Core/libs/Attributes/Field.php`
- New PHP 8 native `#[Field]` attribute class for defining model schemas declaratively on class properties.
- Options: `type`, `required`, `unique`, `min`, `max`.

**Added** `Core/libs/ORM/QueryBuilder.php`
- Full Mongoose-syntax query builder that translates PHP arrays into parameterized SQL queries.
- Supports: `$gt`, `$lt`, `$ne`, `$in` modifiers for precise query targeting.
- Chainable fluent API: `.find()`, `.limit()`, `.sort()`, `.get()`, `.first()`, `.populate()`.

**Replaced** `Core/libs/Model.php`
- Models are now pure Schema Declarations using `#[Field]` Attributes.
- `Model::find(array $conditions)` — static Mongoose-style entry point returning a `QueryBuilder`.
- `Model::create(array $data)` — validates data against `#[Field]` rules before insertion.
- `Model::setConnection($db)` / `Model::getConnection()` — global static PDO singleton shared across all models.
- `Model::validateData()` — Reflection-based automatic property validation.
- Auto table name inference: `User` → `users` (pluralized), or set via `protected static $tableName`.

---

### ⚙️ New Architecture: Auto-Migrations

**Replaced** `Core/libs/Migration.php`
- Removed manual SQL migration file system entirely.
- New `applyMigration()` scans `App/models/`, reflects `#[Field]` attributes from all `Model` subclasses, and executes `CREATE TABLE IF NOT EXISTS` automatically.
- Type mapping: `"string"` → `VARCHAR(255)`, `"int"` → `INT`, with `NOT NULL` and `UNIQUE` constraint support.

**Deleted** `Core/libs/BaseMigration.php`
- No longer needed. Schema is derived automatically from Model definitions.

**CLI command (unchanged):**
```bash
php builder migration:run
```

---

### 🧠 New Architecture: Services Layer

**Added** `Core/libs/Service.php`
- New abstract base class for all application services.
- Extending `Service` automatically provisions `$this->db` using the global ORM database connection (via `Model::getConnection()`), with a Config-based fallback.

**Added** `App/services/` directory
- Autoloader updated in `Core/system/sys.php` to resolve classes from `App/services/` automatically.

**Added** `App/services/ApiService.php`
- Example service demonstrating business logic separation from Models and Controllers.

**Modified** `Core/libs/Controller.php`
- Added `loadService($name)` method enabling controllers to inject service dependencies cleanly via `$this->loadService('api')` → `$this->service`.

---

### 🔒 Security Hardening

**Modified** `Core/libs/Database.php` / `Core/libs/PortableDB.php`
- `update()` and `delete()` `$where` parameters now accept **associative arrays** instead of raw SQL strings.
- All conditions are bound via `PDO::bindValue()` preventing SQL injection natively.

**Modified** `Core/libs/JWT.php`
- Fixed broken static method scoping — `base64UrlEncode()` now called as `self::base64UrlEncode()`.
- Replaced missing `$secret` / legacy `SECRET` constant with `Config::get('ENC_KEY')`.
- Replaced Laravel Carbon dependency with native `time()` based expiration checking.
- `generateToken()` now returns the JWT string rather than directly `echo`-ing JSON.
- Added malformed token guard: validates 3-part structure before processing.
- `validateToken()` now returns the decoded `$payloadData` on success.

**Modified** `Core/system/sys.php`
- Global HTTP Response Headers injected before route dispatch:
  - `Access-Control-Allow-Origin: *`
  - `Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS`
  - `Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With`
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: DENY`
  - `X-XSS-Protection: 1; mode=block`
- CORS preflight `OPTIONS` requests are now resolved immediately with `204 No Content` before the MVC pipeline fires.

---

### 🖼️ View Engine Modernization

**Modified** `Core/libs/View.php`

- **Added** `with(array $data): static` — Fluent, chainable data binding for view templates. Passes variables directly into template scope.
- **Added** `renderMany(array $pages, bool $noInclude = false)` — Clean, documented replacement for `render_mutli()`. Renders multiple view partials in sequence.
- **Added** `layout(string $layoutName)` — Render a named layout shell (e.g. admin dashboard) that controls where content is injected.
- **Fixed** `render_array()` — The previously undefined method that caused fatal crashes is now implemented.
- **Fixed** `render_array()` — Changed from `require_once` to `require` (via `safe_require_fresh()`) so the same partial can be included multiple times on a page.
- **Added** `safe_require(string $file)` — Internal helper checking `file_exists()` before rendering, logging descriptive errors instead of fatal PHP whitescreens.
- **Updated** `Json($value, int $status = 200)` — Now accepts and sets HTTP response status codes via `http_response_code()`.

**XSS Protection Helpers:**

| Method | Context |
|---|---|
| `escape($val)` | HTML element content |
| `escapeAttr($val)` | HTML attribute values |
| `escapeUrl($val)` | href / src / action — blocks `javascript:` / `data:` URIs |
| `escapeJs($val)` | Values inside `<script>` blocks |

---

### 🔧 Configuration & DX Improvements

**Added** `Core/libs/Config.php`
- Centralized configuration registry. Replaces scattered PHP constants across the codebase.
- `Config::load(array $settings)` — Bulk load settings.
- `Config::get(string $key, $default = null)` — Retrieve a setting safely.

**Modified** `App/config/app.php`
- All settings (DB credentials, SMTP, security keys) now loaded into `Config` registry instead of polluting global namespace with `define()`.

**Modified** `Core/libs/FormValidator.php`
- Replaced deprecated PHP 8 `FILTER_SANITIZE_STRING` with `strip_tags()`.

**Added** `App/models/Genre.php` / `App/models/User.php`
- Example Model classes using the new `#[Field]` attribute schema system.

---

### 📚 Documentation

**Updated** `mvc.md`
- Rewrote the Models, Views, and Services sections to reflect all new architecture.
- Added Global API Headers section.
- **Added** Template Engine section with feature table and component examples.

**Added** `documentation/template.md`
- Full Template engine API reference covering components, layouts, scoped CSS, variables, conditionals, and loops.

**Added** `documentation/views.md`
- Full View API reference covering `render()`, `renderMany()`, `layout()`, `with()`, `Json()`, and all XSS escape helpers.

**Added** `documentation/models-and-migrations.md`
- Covers Model `#[Field]` schema definitions, auto-migration workflow, and full CRUD operation examples.

---

### 🎨 New: Svelte-style Template Engine

**Rewritten** `Core/libs/Template.php`
- Fixed broken `$this.property` concatenation syntax (was crashing at runtime) — corrected to `$this->property`.
- **Added** `{variable}` shorthand interpolation with auto HTML escaping.
- **Added** `{#if condition}…{:else}…{/if}` conditional blocks.
- **Added** `{#each array as item}…{/each}` loop blocks.
- **Added** Component system: uppercase HTML tags (`<Card>`, `<Button>`) resolve to `templates/components/Card.html` and support string props (`title="..."`) and reactive props (`title={variable}`).
- **Added** Component `{slot}` — content between component tags is passed as a slot variable.
- **Added** Automatic **Scoped CSS** — `<style>` blocks inside components are extracted, scoped to the component via `data-scope="sXXXXXXXX"` attribute hashing, and collected for injection via `renderScopedCSS()`.
- **Added** `renderScopedCSS()` — outputs all collected scoped component styles as a `<style>` block for placement in your layout's `<head>`.
- **Improved** `includeFiles()` — properly handles `{% extends 'layout.html' %}` parent-child inheritance.
- **Improved** `cache()` — `filemtime()` comparison ensures cache auto-invalidates on template file changes.
- **Added** `mkdir(..., true)` recursive flag for nested cache directory creation.

---

### 🗑️ Removed

- `Core/libs/BaseMigration.php` — Replaced by attribute-driven auto-migrations.
- `App/models/api_model.php` — Business logic extracted to `App/services/ApiService.php`.
- `App/models/component/Genre_Model.php` — Replaced by pure-schema `App/models/Genre.php`.
