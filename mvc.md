### Controller

Controllers are the driving force of a Vannilla Framework. Segments of the URL are directly mapped to a class and function. ie

```php
class  Api extends Controller {	
	function __construct()
	{
		parent::__construct();        
	}
    function index(){
        echo('Welcome to the Api')
    }

    function route2($name){
        echo('Hi, my name is '.$name)
    }
    
}
```
will map to **example.com/api/** and **example.com/api/route2/param** respectivly


###  Views

In Vanilla a view is simply a web page or data response. Views are always loaded by Controllers via the `$this->view` helper.

> For the full View API reference, see [`documentation/views.md`](documentation/views.md).

#### Rendering a Single View

Wraps the template in `App/views/App/Head.php` and `Tail.php` automatically:

```php
$this->view->title = 'Login';
$this->view->render('forms/signin');
```

Skip the wrapper (useful for AJAX or partial responses):

```php
$this->view->render('components/card', noInclude: true);
```

#### Passing Data with `with()`

The preferred, fluent way to bind variables into the view scope:

```php
$this->view->with(['user' => $user, 'posts' => $posts])
           ->render('profile/show');
// In the template: $user and $posts are available directly
```

Legacy assignment style still works:

```php
$this->view->body   = "Hello World";
$this->view->detail = ['title' => 'My Title'];
$this->view->render('main/view');
```

```php
// In App/views/main/view.php:
<?php echo $body; ?>
<?php echo $detail['title']; ?>
```

#### Rendering Multiple Views

```php
// Render several partials in order, wrapped in Head/Tail:
$this->view->with(['user' => $user])
           ->renderMany([
               'components/sidebar',
               'content/feed',
               'widgets/recent'
           ]);
```

#### Layout System

For complex page shells where the layout controls content injection:

```php
// In the controller:
$this->view->with([
    'content' => ['content/stats', 'content/recent_activity']
]);
$this->view->layout('layouts/dashboard');

// In App/views/layouts/dashboard.php:
<?php $this->renderMany($content, noInclude: true); ?>
```

#### JSON Responses (with HTTP status codes)

```php
$this->view->Json(['status' => 'ok', 'data' => $result]);       // 200
$this->view->Json(['error' => 'Not found'], 404);               // 404
$this->view->Json(['error' => 'Unauthorized'], 401);            // 401
```

#### XSS Protection

Always escape user-supplied data before outputting it:

```php
// HTML content:
<?= $this->view->escape($name) ?>

// HTML attributes:
<input value="<?= $this->view->escapeAttr($val) ?>">

// URLs (also blocks javascript: and data: URIs):
<a href="<?= $this->view->escapeUrl($url) ?>">Link</a>

// Inside <script> blocks:
var name = <?= $this->view->escapeJs($name) ?>;
```

#### View File Structure

Views are selected by path relative to `App/views/`:

```
App/
└── views/
    ├── App/
    │   ├── Head.php         ← Auto-prepended wrapper
    │   └── Tail.php         ← Auto-appended wrapper
    ├── layouts/
    │   └── dashboard.php
    ├── components/
    │   └── sidebar.php
    └── forms/
        └── signin.php
```


### Models

Models in Vanilla Framework have been upgraded to utilize a modern, Mongoose-like Active Record ORM powered by PHP 8 Attributes. Models act as pure **Schema Declarations** mapping directly to your database tables.

```php
<?php

use Core\Attributes\Field;

class User extends Model
{
    // The table name defaults to the plural form of the class name ('users'), 
    // or you can set it explicitly via: protected static $tableName = 'users';

    #[Field(type: "string", required: true)]
    public string $name;

    #[Field(type: "string", unique: true)]
    public string $email;

    #[Field(type: "int", min: 18)]
    public int $age;
}
```

The database connection is managed globally by the framework, and the ORM provides an intuitive chainable syntax:

#### Querying Data

Retrieve records using MongoDB-like condition arrays:

```php
// Find all users over the age of 18
$adults = User::find([
    'age' => ['$gt' => 18]
])->limit(10)->get();

// Find a single user by ID
$user = User::find(['user_id' => 1])->first();
```

Supported modifiers include `$gt` (greater than), `$lt` (less than), `$ne` (not equal), and `$in` (in array).

#### Creating Data

The `create` method automatically validates incoming data against your `#[Field]` attributes before insertion:

```php
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 25
]);
```

---

### Services Layer

To maintain strict separation of concerns, business logic should not reside in Models or Controllers. Instead, use **Services**.

Services are classes that extend the core `Service` base class. By extending `Service`, your class automatically inherits a secure, connection-ready `$this->db` instance.

#### Creating a Service

Create your service in `App/services/`:

```php
<?php

class ApiService extends Service
{
    public function getAdultUsers() {
        // You can use the ORM directly here...
        return User::find(['age' => ['$gt' => 18]])->get();
    }

    public function complexQuery() {
        // ...or use the inherited raw Database object for complex joins
        return $this->db->select("SELECT * FROM users JOIN roles ON users.role_id = roles.id");
    }
}
```

#### Calling Services from Controllers

Controllers can elegantly inject services using the `$this->loadService()` method:

```php
class Api extends Controller 
{
    public function __construct()
    {
        parent::__construct();
        // Automatically loads and instantiates App/services/ApiService.php
        $this->loadService('api'); 
    }

    public function index()
    {
        $data = $this->service->getAdultUsers();
        $this->view->Json($data);
    }
}
```

---
### Legacy Database Functions

For raw database interactions using `$this->db` (from extending `Service` or old-style `Model`):

- **Select Multiple:** `$this->db->select('SELECT * FROM users');`
- **Select Single:** `$this->db->select_one('SELECT * FROM users WHERE id = :id', [':id' => 1]);`
- **Insert:** `$this->db->insert('users', ['name' => 'John', 'age' => 25]);`
- **Update:** `$this->db->update('users', ['name' => 'Johnny'], ['id' => 1]);`
- **Delete:** `$this->db->delete('users', ['id' => 1]);`

*Note: `update` and `delete` conditions must now take associative arrays `['target_col' => 'value']` to protect against SQL injection.*

---
### Utilities
- `Plugin($name)` - Load a plugin
- `Helper($name)` - Load a helper
- `$this->redirect($location)` - Redirect to a page route natively.

---
### Global API Headers

By default, the framework natively handles API connectivity boundaries out-of-the-box upon bootstrap:

- **Universal CORS:** Requests automatically receive `Access-Control-Allow-Origin: *` ensuring API consumers can fetch data seamlessly.
- **Preflight Check Optimization:** `OPTIONS` browser preflights return a `204 No Content` natively right inside `sys.php` without booting the MVC pipeline, saving heavy server payload execution.
- **Security Posture:** Framework globally injects `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, and `X-XSS-Protection: 1; mode=block` into all View/Data rendering bounds to inherently disable MIME-sniffing loopholes and Clickjacking inside your MVC responses natively.

---

### Template Engine

The framework includes a Svelte/Vue-inspired template engine (`Core/libs/Template.php`) for building component-driven HTML views powered by `.html` files.

> For the full Template API reference, see [`documentation/template.md`](documentation/template.md).

Templates are accessible via `$this->template` inside any controller:

```php
$this->template->view("home.html", [
    "user" => $currentUser,
    "posts" => $posts
]);
```

#### Key Features

| Feature | Syntax |
|---|---|
| Variables with auto XSS escaping | `{name}` |
| Conditionals | `{#if condition}…{/if}` |
| Loops | `{#each items as item}…{/each}` |
| Layout inheritance | `{% extends 'layouts/main.html' %}` |
| Block / yield slots | `{% block content %}…{% endblock %}` |
| Components | `<Card title="Hello">slot content</Card>` |
| Reactive props | `<Card title={variable}>` |
| Scoped CSS (automatic) | `<style>` blocks inside components |

#### Component Example

Create `App/templates/components/Card.html`:
```html
<style>
    .card { padding: 20px; border-radius: 8px; }
</style>

<div class="card">
    <h2>{title}</h2>
    {slot}
</div>
```

Use it in any template:
```html
<Card title="Welcome">
    {#each posts as post}
        <p>{post.title}</p>
    {/each}
</Card>
```


