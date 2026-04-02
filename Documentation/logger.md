# Vanilla Framework: Unified Logger

The `Logger` class is a cross-environment utility designed to provide consistent, professional-looking feedback in both terminal (CLI) and browser (Web) contexts. It replaces raw `echo` statements with an intelligent, color-coded alert system.

---

## 1. Basic Usage

The `Logger` provides four standardized methods for different message levels. Each level is automatically formatted with appropriate colors and icons depending on your environment.

### Standard Methods:

```php
Logger::Info("Scanning directories...");
Logger::Success("Operation completed successfully.");
Logger::Warning("This action might have side effects.");
Logger::Error("Failed to connect to the database.");
```

---

## 2. Environment Detection

The framework automatically detects if it's running via `STDOUT` (Terminal) or a web server.

### CLI (Terminal) Output
In the terminal, the `Logger` uses **ANSI escape codes** to provide high-visibility status tags:
- `[INFO]` (Cyan)
- `[SUCCESS]` (Green)
- `[WARNING]` (Yellow)
- `[ERROR]` (Red)

### Web (Browser) Output
In the browser, the `Logger` uses **HTML/CSS** styled `<span>` tags to ensure developers see the same color-coding directly in their views or error reports.

---

## 3. Backward Compatibility (The `Log` Alias)

To ensure that legacy code and existing projects don't break, the framework provides a `Log` class that acts as a direct alias for `Logger`. 

```php
// Old code continues to work perfectly:
Log::Error("Something went wrong!"); 

// New code is encouraged to use:
Logger::Error("Something went wrong!");
```

---

## 4. Key Improvements in 2.2.0

Previously, many core libraries (like `Migration.php` and `Helper.php`) used raw `echo` statements with manual strings like `[OK]` or `[ERROR]`. These have all been standardized to use the `Logger` class, resulting in:
- **Consistent Visuals**: All terminal feedback now follows a unified color palette.
- **Improved Visibility**: High-priority errors and instructions (like missing configuration steps) are now impossible to miss in the console.
- **Maintainability**: If the logging logic needs to change (e.g., adding file logging), it only needs to be updated in one place.
