# Vanilla Framework: Models & Migrations

The Vanilla Framework uses a modern, Mongoose-like **Active Record ORM** powered by PHP 8 Attributes. This system provides an elegant, schema-first approach to database management, completely eliminating the need to write raw SQL schemas or manual migration scripts.

---

## 0. Database Configuration

The framework supports multiple database drivers. You can configure your database in `App/config/db.conf` (JSON format) and `App/config/app.php`.

### Valid Configuration Keys:
- `DB_TYPE`: `"mysql"` or `"sqlite"`.
- `DB_NAME`: The database name (for MySQL) or the filename (for SQLite).
- `DB_HOST`: Server address (for MySQL). For SQLite, this can be empty (defaults to `ROOT/DataBase/`).
- `DB_USER` / `DB_PASS`: Credentials (for MySQL).

### Example JSON (`db.conf`):
```json
{
  "DB_TYPE": "sqlite",
  "DB_NAME": "my_project",
  "DB_HOST": "",
  "DB_USER": "",
  "DB_PASS": ""
}
```

---

## 1. Defining Models (Schemas)

Models live in `App/models/`. Unlike legacy frameworks where models hold business logic and messy database queries, our Models act strictly as **Schema Declarations**.

We use the generic `#[Field]` attribute to define the structural type, validation constraints, and properties of a database column natively within the class.

### Example: The `User` Model

```php
<?php

use Core\Attributes\Field;

class User extends Model
{
    // Optional: Explicitly define the table name. 
    // If omitted, the framework pluralizes the class name (e.g. 'users').
    protected static $tableName = 'users';

    #[Field(type: "string", required: true)]
    public string $name;

    #[Field(type: "string", unique: true)]
    public string $email;

    #[Field(type: "int", min: 18)]
    public int $age;
}
```

**Available Field Options:**

| Option     | Type    | Default  | Description                                              |
|------------|---------|----------|----------------------------------------------------------|
| `type`     | string  | `'string'` | Generic data type (`"string"`, `"int"`, `"text"`, `"boolean"`, `"date"`, `"datetime"`, `"float"`) |
| `required` | bool    | `false`  | If true, column is `NOT NULL`                            |
| `unique`   | bool    | `false`  | Adds a `UNIQUE` constraint                               |
| `nullable` | bool    | `true`   | Whether the column allows `NULL` values                  |
| `default`  | mixed   | `null`   | SQL `DEFAULT` value for the column                       |
| `min`/`max`| ?int    | `null`   | For `int` types, numeric boundaries validated before insertion |

**Type Mapping:**

| Field Type          | MySQL Type     | SQLite Type    |
|---------------------|----------------|----------------|
| `"string"` (default)| `VARCHAR(255)` | `VARCHAR(255)` |
| `"int"`             | `INT`          | `INTEGER`      |
| `"text"`            | `TEXT`         | `TEXT`         |
| `"boolean"` / `"bool"` | `TINYINT(1)` | `INTEGER`      |
| `"date"`            | `DATE`         | `DATE`         |
| `"datetime"`        | `DATETIME`     | `DATETIME`     |
| `"float"` / `"double"` | `DOUBLE`   | `DOUBLE`       |

---

## 2. Auto-Migrations (Code-First Schema Sync)

You **never** need to write `CREATE TABLE` or `ALTER TABLE` statements manually. The Migration engine scans `App/models/`, reflects over your `#[Field]` attributes, and **synchronizes** the MySQL schema automatically — including adding, modifying, and dropping columns.

### Running Migrations

```bash
php vanilla migration run
```

### How it works:

1. `Migration::applyMigration` scans all PHP files in `App/models/`.
2. For each class that `extends Model`, it reads the `#[Field]` attributes via PHP Reflection.
3. **If the table doesn't exist** — generates a `CREATE TABLE` statement (using `AUTO_INCREMENT` for MySQL or `AUTOINCREMENT` for SQLite).
4. **If the table already exists** — introspects the database using driver-aware queries (e.g. `SHOW COLUMNS` vs `PRAGMA table_info`):
   - **New fields** → `ALTER TABLE ... ADD COLUMN` (Works for all)
   - **Changed fields** (type, nullability, unique) →
     - **MySQL**: `ALTER TABLE ... MODIFY COLUMN`
     - **SQLite**: **Automatic Table Reconstruction**. Because SQLite doesn't natively support modifying columns, the framework automatically creates a temporary table with the new schema, migrates your data, and swaps the tables.
   - **Removed fields** → 
     - **MySQL**: `ALTER TABLE ... DROP COLUMN`
     - **SQLite**: Automatic Table Reconstruction.
5. Unique indexes are managed automatically across both drivers.

### Other Migration Commands

| Command                      | Description                                                    |
|------------------------------|----------------------------------------------------------------|
| `php vanilla migration run`     | Sync all model schemas to the database                      |
| `php vanilla migration clear`   | **Drop** the entire database and recreate it (destructive!) |
| `php vanilla migration refresh` | Clear + re-run all migrations from scratch                  |

*If you add, change, or remove a model property, simply re-run `php vanilla migration run` to bring the database in sync!*


---

## 3. CRUD Functionality (Data Interaction)

The ORM provides a Mongoose-like fluent query builder. Because the Framework globally provisions the `$this->db` PDO instance on bootstrap, you do not need to manually configure Database instances inside your logic.

### Create (Inserting Data)

The `Model::create()` method automatically evaluates the passed array against your `#[Field]` attributes. If validation fails (e.g. `age < 18`), it protects the database and throws an Exception natively.

```php
$user = User::create([
    'name' => 'Jane Appleseed',
    'email' => 'jane@example.com',
    'age' => 24
]);

// Returns the validated object that was inserted.
echo $user->name;
```

### Read (Querying Data)

The `Model::find()` static method initializes the QueryBuilder. It accepts an associative array mapped seamlessly to Mongo-like operators.

**Basic Matching:**
```php
// SELECT * FROM users WHERE age = 24
$users = User::find(['age' => 24])->get();
```

**Advanced Modifiers (`$gt`, `$lt`, `$ne`, `$in`):**
```php
// Using Mongoose-syntax for exact targeting
$adults = User::find([
    'age' => ['$gt' => 18]     // age > 18
])
->limit(10)                    // Limit sequence
->get();                       // Fetch all results

print_r($adults);
```

**Fetching a Single Record:**
```php
// Retrieve the first associated record natively
$singleUser = User::find(['email' => 'jane@example.com'])->first();

if ($singleUser) {
    echo $singleUser['name'];
}
```

### Update (Modifying Data)

For advanced partial updates, you can fetch the raw Service Database inherited instance from your `App/services`.

```php
class UserService extends Service 
{
    public function updateUserEmail($userId, $newEmail) {
        // Safe Parameterized Querying natively protects against SQL injection.
        $this->db->update(
            'users',                            // Table
            ['email' => $newEmail],             // Data payload
            ['user_id' => $userId]              // Assosciative Matcher
        );
    }
}
```

### Delete (Removing Data)

Similarly, deletion securely binds associative matchers inside your Logic Services:

```php
class UserService extends Service 
{
    public function removeUser($userId) {
        // Deletes securely using parameterized binding natively.
        $this->db->delete('users', ['user_id' => $userId]);
    }
}
```
