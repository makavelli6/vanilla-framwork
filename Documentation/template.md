# Vanilla Framework: Template Engine

The `Template` class (`Core/libs/Template.php`) is a Svelte/Vue-inspired template engine for the Vanilla Framework. It compiles `.html` template files into cached PHP and provides components, layout inheritance, reactive props, loops, conditionals, and automatic scoped CSS.

---

## 1. Directory Structure

```
App/
└── templates/
    ├── home.html
    ├── layouts/
    │   └── main.html
    └── components/
        ├── Card.html
        └── Button.html
```

---

## 2. Basic Usage

The template is accessible from any controller via `$this->template`:

```php
$this->template->view("home.html", [
    "name"  => "Joshua",
    "users" => [
        ["name" => "Alice"],
        ["name" => "Bob"]
    ]
]);
```

The engine compiles the template, caches it, and renders the output automatically.

---

## 3. Variable Interpolation

Variables are output using `{variable}` syntax. They are **automatically HTML-escaped** to prevent XSS:

```html
<h1>Hello {name}</h1>
```

---

## 4. Conditionals

```html
{#if loggedIn}
    <p>Welcome back!</p>
{:else}
    <p>Please log in.</p>
{/if}
```

---

## 5. Loops

```html
<ul>
{#each users as user}
    <li>{user.name}</li>
{/each}
</ul>
```

---

## 6. Layout Inheritance

Layouts define common page structure (shell HTML, head, footer etc.).

**`templates/layouts/main.html`:**
```html
<!DOCTYPE html>
<html>
<head>
    <title>My App</title>
    <?php echo $this->renderScopedCSS(); ?>
</head>
<body>
    {% yield content %}
</body>
</html>
```

**Page template (`templates/home.html`):**
```html
{% extends 'layouts/main.html' %}

{% block content %}
    <h1>Dashboard</h1>
{% endblock %}
```

---

## 7. Components

Components are reusable HTML fragments stored in `templates/components/`. **Component names must start with an uppercase letter.**

**`templates/components/Card.html`:**
```html
<div class="card">
    <h2>{title}</h2>
    <div>{slot}</div>
</div>
```

**Using a component in a template:**
```html
<Card title="Latest Posts">
    Content inside the card goes here.
</Card>
```

The content between the tags is automatically passed as `{slot}`.

---

## 8. Component Props

Props pass data into components. String props use `"..."`, reactive (variable) props use `{...}`:

```html
<!-- Static string prop -->
<Card title="Hello World">...</Card>

<!-- Reactive prop — binds a template variable -->
<Card title={pageTitle}>...</Card>
```

---

## 9. Nested Components

Components can be freely nested:

```html
{#each users as user}
    <Card title={user.name}>
        <Button label="View Profile">View</Button>
    </Card>
{/each}
```

---

## 10. Scoped CSS (Automatic)

CSS written inside a component's `<style>` block is **automatically scoped** to that component. It will not leak to other elements on the page.

**In `Card.html`:**
```html
<style>
    .card {
        padding: 20px;
        border-radius: 8px;
    }
</style>

<div class="card">
    {slot}
</div>
```

The engine outputs:
```css
.card[data-scope="s3a9c1f2"] {
    padding: 20px;
    border-radius: 8px;
}
```

And the rendered HTML receives:
```html
<div class="card" data-scope="s3a9c1f2">...</div>
```

> Global styles (targeting `body` or `html`) are never scoped.

### Rendering Collected CSS

Call `renderScopedCSS()` once in your layout's `<head>` to inject all collected component styles:

```html
<?php echo $this->renderScopedCSS(); ?>
```

---

## 11. Template Caching

Compiled templates are cached to `Store/Cache/Pages/` for performance. The cache automatically invalidates when the source template file changes.

```php
// Disable cache during development:
$this->template->cache_enabled = false;

// Clear all cached templates:
$this->template->clearCache();
```

---

## 12. Template Syntax Reference

| Syntax | Purpose |
|---|---|
| `{variable}` | Output an escaped variable |
| `{{ $php }}` | Output raw PHP expression |
| `{{{ $raw }}}` | Output HTML-escaped PHP expression |
| `{% php code %}` | Execute arbitrary PHP |
| `{#if condition}` | Conditional block |
| `{:else}` | Else branch |
| `{/if}` | End conditional |
| `{#each list as item}` | Loop over array |
| `{/each}` | End loop |
| `<Component prop="val">` | Use a component |
| `{slot}` | Render slot content in a component |
| `{% extends 'layout.html' %}` | Inherit a layout |
| `{% block name %}...{% endblock %}` | Define a block |
| `{% yield name %}` | Yield a block inside a layout |
| `@parent` | Append to parent block content |

---

## 13. Full Example

**Controller:**
```php
$this->template->view("home.html", [
    "users" => [
        ["name" => "Alice"],
        ["name" => "Bob"]
    ]
]);
```

**`templates/home.html`:**
```html
{% extends 'layouts/main.html' %}

{% block content %}
    <h1>User List</h1>

    {#each users as user}
        <Card title={user.name}>
            Profile content here.
        </Card>
    {/each}
{% endblock %}
```