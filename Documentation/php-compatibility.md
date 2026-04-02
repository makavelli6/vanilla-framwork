# Vanilla Framework: PHP & Compatibility

This document outlines the framework's approach to maintaining compatibility with modern PHP versions (8.2, 8.3, 8.4+) while preserving its core architectural features.

---

## 1. Dynamic Properties (PHP 8.2+)

PHP 8.2 introduced a significant change where creating dynamic properties on classes is deprecated unless the class is specifically marked to allow them. This has major implications for the framework's standard "Active Record" and "View" patterns.

### The Problem

In previous versions, a controller could simply assign a variable to its view like this:
```php
$this->view->title = 'Home Page';
```
Since the `View` class didn't explicitly have a `public $title` property, PHP 8.2 would trigger a **Deprecated** warning.

### The Solution: `#[AllowDynamicProperties]`

The `Core/libs/View.php` class is now decorated with the `#[AllowDynamicProperties]` attribute. This is the idiomatic way to handle this when a class is designed to act as a dynamic data container.

### The Solution: Explicit Property Declaration

For core classes like `Controller`, we have explicitly declared the common properties to ensure maximum performance and zero warnings:

```php
abstract class Controller
{
    public $view;
    public $template;
    public $mail;
    public $model;
    public $service;
}
```

---

## 2. Global Configuration & Templates

While the framework uses a modern `Config` registry for most settings, many view templates (`App/views/*.php`) rely on global constants like `URL` and `SITE` for convenient asset linking.

### Global Asset Linking

The `App/config/app.php` file explicitly defines these constants to ensure your templates can easily link to CSS, JS, and Images:

```php
// In app.php:
define('URL',  $db['URL'] ?? '/');
define('SITE', $db['SITE'] ?? '/');

// In App/views/App/Head.php (View Template):
<link rel="stylesheet" href="<?= URL ?>public/css/main.css" />
```

### Recommendation

Always use the `Config` registry for application logic, but feel free to use `URL` and `SITE` in your HTML views as they are now guaranteed to be globally defined during the bootstrap process.

---

## 3. Environment Compatibility

When running the framework's development server via:
```bash
php vanilla init server
```
The application correctly initializes all global paths and configuration, ensuring that `URL` and `SITE` are correctly populated based on your `App/config/db.conf` settings.
