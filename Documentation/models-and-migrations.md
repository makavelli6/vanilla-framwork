# Vanilla Framework: Models & Migrations

The Vanilla Framework uses a modern, Mongoose-like **Active Record ORM** powered by PHP 8 Attributes. This system provides an elegant, schema-first approach to database management, completely eliminating the need to write raw SQL schemas or manual migration scripts.

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
- `type`: The generic data type (`"string"`, `"int"`, etc.)
- `required`: Boolean representing if the field cannot be null.
- `unique`: Boolean representing a UNIQUE constraint in the database.
- `min` / `max`: For `int` types, defines numeric boundaries dynamically validated before insertion.

---

## 2. Auto-Migrations (Code-First Schemas)

You **never** need to write `CREATE TABLE` manual migration files again. The framework contains an automated Migration engine that scans your `App/models/` directory, reflects over your `#[Field]` attributes, and builds the MySQL database structures dynamically!

### Running Migrations

To synchronize your PHP Models with the Database, simply run the CLI builder command:

```bash
php builder migration:run
```

**How it works:**
1. The `Migration::applyMigration` compiler opens `App/models/`.
2. It finds all valid classes that `extend Model`.
3. It maps `#[Field(type: 'string')]` to MySQL `VARCHAR(255)`.
4. It maps `#[Field(type: 'int')]` to MySQL `INT`.
5. It applies constraints (`NOT NULL`, `UNIQUE`) automatically and executes `CREATE TABLE IF NOT EXISTS`.

*If you change a model property later, just re-run the command to ensure the schema remains synchronized!*

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
