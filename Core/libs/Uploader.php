<?php
/**
 * Uploader - A reusable class for handling file uploads.
 */
class Uploader
{
    private $allowedExtensions = ['png', 'jpg', 'jpeg'];
    private $maxFileSize = 2000000; // Default 2MB
    private $targetDir = '';

    /**
     * Constructor specifically for setting default configuration.
     */
    public function __construct(string $targetDir = '')
    {
        $this->targetDir = $targetDir;
    }

    /**
     * Set allowed file extensions.
     */
    public function setAllowed(array $extensions): self
    {
        $this->allowedExtensions = array_map('strtolower', $extensions);
        return $this;
    }

    /**
     * Set maximum file size in bytes.
     */
    public function setMaxSize(int $bytes): self
    {
        $this->maxFileSize = $bytes;
        return $this;
    }

    /**
     * Set the target upload directory.
     */
    public function setTargetDir(string $dir): self
    {
        $this->targetDir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * Process the file upload. 
     * 
     * @param array $file The $_FILES['input_name'] array.
     * @return array Result containing status, path, and error message.
     */
    public function upload(array $file): array
    {
        $result = ['success' => false, 'error' => null, 'path' => null];

        // 1. Basic PHP Upload Validation
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $result['error'] = $this->getUploadErrorMessage($file['error']);
            return $result;
        }

        // 2. Validate Extension
        $fileName = $file['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $this->allowedExtensions)) {
            $result['error'] = 'File format not supported: ' . $fileExt;
            return $result;
        }

        // 3. Validate Size
        $fileSize = $file['size'];
        if ($fileSize > $this->maxFileSize) {
            $result['error'] = 'File is too large: ' . number_format($fileSize / 1024, 2) . 'KB';
            return $result;
        }

        File::handleDir($this->targetDir);

        // 5. Initial Upload
        $finalFileName = self::getHashedName($file['tmp_name'], $fileExt);
        $finalDest = $this->targetDir . $finalFileName;

        if (self::exists($finalFileName, $this->targetDir)) {
            $result['path'] = $finalDest;
            $result['success'] = true;
            $result['message'] = 'Existing file found via hash.';
            return $result;
        }

        if (move_uploaded_file($file['tmp_name'], $finalDest)) {
            $result['path'] = $finalDest;
            $result['success'] = true;
        } else {
            $result['error'] = 'Failed to move uploaded file.';
        }

        return $result;
    }

    /**
     * Generate a hashed filename based on file content.
     */
    public static function getHashedName(string $filePath, string $extension): string
    {
        return md5_file($filePath) . '.' . ltrim($extension, '.');
    }

    /**
     * Check if a specific file exists in the target directory.
     */
    public static function exists(string $fileName, string $targetDir): bool
    {
        $path = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
        return file_exists($path);
    }

    /**
     * Delete a file from the target directory.
     */
    public static function delete(string $fileName, string $targetDir): bool
    {
        $path = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
        return File::delete_file($path);
    }

    /**
     * Map PHP upload error codes to human-readable messages.
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds maximum size limits.';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded.';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk.';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload.';
            default:
                return 'Unknown upload error.';
        }
    }
}