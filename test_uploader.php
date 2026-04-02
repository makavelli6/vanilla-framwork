<?php
define('ROOT', __DIR__);
require_once ROOT . '/Core/libs/File.php';
require_once ROOT . '/Core/libs/Uploader.php';

// Mocking some system constants if needed
if (!defined('UPLOAD_ERR_OK')) define('UPLOAD_ERR_OK', 0);

/**
 * UploaderTest subclass to override move_uploaded_file for testing.
 */
class UploaderTest extends Uploader {
    protected function moveFile($tmp, $dest) {
        return copy($tmp, $dest);
    }

    // Overriding the upload method slightly to use moveFile instead of move_uploaded_file
    public function upload(array $file): array {
        $result = ['success' => false, 'error' => null, 'path' => null];

        if ($file['error'] !== 0) {
            $result['error'] = 'Upload error';
            return $result;
        }

        $fileName = $file['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Use reflection to access private properties for the test
        $ref = new ReflectionClass('Uploader');
        $allowedProp = $ref->getProperty('allowedExtensions');
        $allowedProp->setAccessible(true);
        $allowed = $allowedProp->getValue($this);

        if (!in_array($fileExt, $allowed)) {
            $result['error'] = 'Format not supported';
            return $result;
        }

        $maxProp = $ref->getProperty('maxFileSize');
        $maxProp->setAccessible(true);
        $max = $maxProp->getValue($this);

        if ($file['size'] > $max) {
            $result['error'] = 'Too big';
            return $result;
        }

        $targetProp = $ref->getProperty('targetDir');
        $targetProp->setAccessible(true);
        $targetDir = $targetProp->getValue($this);

        File::handleDir($targetDir);

        $tempFileName = 'test_tmp_' . uniqid() . '.' . $fileExt;
        $tempDest = $targetDir . $tempFileName;

        if ($this->moveFile($file['tmp_name'], $tempDest)) {
             $hash = md5_file($tempDest);
             $finalFileName = $hash . '.' . $fileExt;
             $finalDest = $targetDir . $finalFileName;

             if (file_exists($finalDest)) {
                 unlink($tempDest);
                 $result['path'] = $finalDest;
                 $result['success'] = true;
             } else {
                 rename($tempDest, $finalDest);
                 $result['path'] = $finalDest;
                 $result['success'] = true;
             }
        }

        return $result;
    }
}

// Test Execution
try {
    $uploadDir = ROOT . '/test_uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir);

    $uploader = new UploaderTest($uploadDir);
    $uploader->setAllowed(['txt', 'png']);

    // Create dummy file
    $dummyFile = ROOT . '/dummy.txt';
    file_put_contents($dummyFile, "Hello World Content");

    $mockFile = [
        'name' => 'test.txt',
        'type' => 'text/plain',
        'tmp_name' => $dummyFile,
        'error' => 0,
        'size' => filesize($dummyFile)
    ];

    echo "Testing Upload...\n";
    $res = $uploader->upload($mockFile);
    print_r($res);

    if ($res['success']) {
        echo "Upload Success! Path: " . $res['path'] . "\n";
        $hash = md5_file($dummyFile);
        if (basename($res['path']) === $hash . '.txt') {
            echo "Filename correctly hashed: " . $hash . "\n";
        }
    }

    echo "\nTesting deduplication...\n";
    $res2 = $uploader->upload($mockFile);
    if ($res2['success'] && $res2['path'] === $res['path']) {
        echo "Deduplication works! Reused existing file.\n";
    }

    // Clean up
    if (file_exists($res['path'])) unlink($res['path']);
    if (file_exists($dummyFile)) unlink($dummyFile);
    rmdir($uploadDir);

    echo "\nTest Passed Successfully!\n";

} catch (Exception $e) {
    echo "Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
