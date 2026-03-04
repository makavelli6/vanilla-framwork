# Vanilla Framework: View System

The `View` class (`Core/libs/View.php`) is the framework's rendering engine. It handles HTML page rendering, partial composition, JSON API responses, and provides built-in XSS security helpers.

---

## 1. Rendering a Single View

Standard usage — wraps the template in `App/views/App/Head.php` and `Tail.php` automatically:

```php
// In your Controller:
$this->view->title = 'Home Page';      // Variables set this way...
$this->view->render('home/index');     // ...are available directly in the template
```

```php
// In App/views/home/index.php:
<h1><?= $title ?></h1>
```

To render **without** the Head/Tail wrapper (e.g. for AJAX partials):

```php
$this->view->render('components/card', noInclude: true);
```

---

## 2. Explicit Data Binding with `with()`

The preferred way to pass data to a view. Fluent — can be chained directly onto a render call:

```php
$this->view
    ->with(['user' => $user, 'posts' => $posts])
    ->render('profile/show');
```

Inside `App/views/profile/show.php`, `$user` and `$posts` are scoped directly — no `$this->...` needed.

You can call `with()` multiple times and data accumulates:

```php
$this->view->with(['section' => 'Dashboard']);
$this->view->with(['user' => $currentUser]);
$this->view->render('layouts/main');
```

---

## 3. Rendering Multiple Views

### `renderMany()` — Clean, Recommended Approach

Renders an ordered array of view partials sequentially, wrapped in Head/Tail by default:

```php
$this->view->with(['user' => $user])
    ->renderMany([
        'components/sidebar',
        'content/feed',
        'widgets/recent'
    ]);
```

Partials only (no Head/Tail):

```php
$this->view->renderMany(['nav/top', 'content/body'], noInclude: true);
```

### `layout()` — Custom Layout Shells

For complex page shells (e.g. admin dashboards) where the layout controls where content is injected:

```php
// In your controller:
$this->view->with([
    'title'   => 'Dashboard',
    'content' => ['content/stats', 'content/recent_activity']
]);
$this->view->layout('layouts/dashboard');
```

```php
// In App/views/layouts/dashboard.php:
<div class="main">
    <?php $this->renderMany($content, noInclude: true); ?>
</div>
```

---

## 4. JSON API Responses

Send JSON data with an optional HTTP status code:

```php
// 200 success (default):
$this->view->Json(['status' => 'ok', 'data' => $result]);

// Specific status codes:
$this->view->Json(['error' => 'Not found'], 404);
$this->view->Json(['error' => 'Unauthorized'], 401);
$this->view->Json(['error' => 'Validation failed', 'fields' => $errors], 422);
```

---

## 5. XSS Protection

> [!IMPORTANT]
> Always escape untrusted data before outputting it. Use the appropriate helper for the rendering context.

### HTML Body Content — `escape()`

For text displayed inside HTML elements:

```php
<p>Welcome, <?= $this->view->escape($user['name']) ?></p>
```

### HTML Attribute Values — `escapeAttr()`

For dynamic values inside HTML attributes:

```php
<input type="text" value="<?= $this->view->escapeAttr($defaultValue) ?>">
<div class="card <?= $this->view->escapeAttr($extraClass) ?>">
```

### URLs — `escapeUrl()`

For values used as `href`, `src`, or `action` URLs. **Automatically blocks `javascript:`, `data:`, and `vbscript:` injection attempts:**

```php
<a href="<?= $this->view->escapeUrl($profileUrl) ?>">View Profile</a>
<img src="<?= $this->view->escapeUrl($avatarUrl) ?>">
```

### JavaScript Context — `escapeJs()`

For values embedded inside `<script>` tags:

```php
<script>
    const config = {
        userId: <?= $this->view->escapeJs($userId) ?>,
        name:   <?= $this->view->escapeJs($name) ?>
    };
</script>
```

---

## 6. View Directory Structure

```
App/
└── views/
    ├── App/
    │   ├── Head.php          # Global HTML <head> wrapper
    │   └── Tail.php          # Global footer / script wrapper
    ├── layouts/
    │   └── dashboard.php     # Custom layout shells
    ├── components/
    │   ├── sidebar.php       # Reusable partials
    │   └── card.php
    └── home/
        └── index.php         # Page-level templates
```
