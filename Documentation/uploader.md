# Vanilla Framework: File Uploader

The `Uploader` class provides a secure, reusable, and configurable way to handle file uploads in the Vanilla Framework. It includes built-in validation for extensions and file sizes, as well as automatic deduplication using MD5 content hashing.

---

## 1. Basic Usage (Fluent API)

The uploader is designed to be highly configurable using a fluent interface. You can set the target directory, allowed extensions, and maximum file size before processing the upload.

### Example in a Controller or Service:

```php
use Core\Libs\Uploader;

public function uploadProfile()
{
    $uploader = new Uploader('uploads/profiles');
    
    $result = $uploader->setAllowed(['png', 'jpg', 'jpeg'])
                       ->setMaxSize(1024 * 500) // 500 KB
                       ->upload($_FILES['avatar']);

    if ($result['success']) {
        echo "File uploaded to: " . $result['path'];
    } else {
        echo "Error: " . $result['error'];
    }
}
```

---

## 2. Configuration Options

| Method | Description | Default |
|---|---|---|
| `setAllowed(array $ext)` | List of allowed file extensions (case-insensitive). | `['png', 'jpg', 'jpeg']` |
| `setMaxSize(int $bytes)` | Maximum file size allowed in bytes. | `2,000,000` (2 MB) |
| `setTargetDir(string $dir)` | The directory where files will be stored. | Must be set. |

---

## 3. Static Helpers (Deduplication & Management)

The `Uploader` includes static methods that are useful for checking if a file has already been uploaded or for deleting files by their content hash.

### Method Reference:

#### `Uploader::getHashedName(string $filePath, string $extension)`
Generates a filename based on the MD5 hash of the file's content. Useful for checking duplicates before moving the file.

#### `Uploader::exists(string $fileName, string $targetDir)`
Checks if a file with the given name (hashed or otherwise) exists in the target directory.

#### `Uploader::delete(string $fileName, string $targetDir)`
Deletes the specified file from the upload directory using the `File` library.

---

## 4. Key Features

- **MD5 Deduplication**: Automatically detects if the same file content has been uploaded before and reuses the existing file path.
- **Auto-Directory Creation**: Automatically creates the target directory if it doesn't exist.
- **Upload Error Mapping**: Converts complex PHP upload error codes (like `UPLOAD_ERR_INI_SIZE`) into human-readable messages.
- **Sanitized Filenames**: Ensures filenames are based on content hashes, preventing directory traversal and filename conflicts.
