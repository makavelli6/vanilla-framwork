<?php 	

class File
{
	public static function copy_file($file, ?string $destination='', ?string $file2 ='') {
		if (!file_exists($file)) return false;
		return copy($file, rtrim($destination, '/') . '/' . ltrim($file2, '/'));
	}

	public static function make_dir($destination='', ?string $dir='') {
        $path = rtrim($destination, '/') . '/' . trim($dir, '/');
        if (is_dir($path)) return true;
		return mkdir($path, 0777, true);
	}

	public static function delete_dir($dirname) {
        if (!is_dir($dirname)) return false;
		return rmdir($dirname);
	}

	public static function move_file($file1, $destination) {
        if (!file_exists($file1)) return false;
		return rename($file1, $destination);
	}

	public static function delete_file($file) {
        if (!file_exists($file)) return false;
		return unlink($file);
	}

	public static function rename_dir($destination='', ?string $oldname='', ?string $newname='') {
        $oldPath = rtrim($destination, '/') . '/' . trim($oldname, '/');
        $newPath = rtrim($destination, '/') . '/' . trim($newname, '/');
        if (!is_dir($oldPath)) return false;
		return rename($oldPath, $newPath);
	}

	public static function rename_file($oldname, $newname) {
        if (!file_exists($oldname)) return false;
		return rename($oldname, $newname);
	}

	public static function load_content($value) {
		if (!file_exists($value)) {
			return false;
        }
		return file_get_contents($value);
	}

	public static function replace_string_in_file($filename, $string_to_replace, $replace_with) {
        if (!file_exists($filename)) return false;
		$content = file_get_contents($filename);
		$content = str_replace($string_to_replace, $replace_with, $content);
		return file_put_contents($filename, $content) !== false;
	}

	public static function handleFile($dir) {
		if (!file_exists($dir)) {
			// Using c to just create or open without truncating
			$handle = @fopen($dir, "c");
			if ($handle === false) {
				return false;
			}
			fclose($handle);
            return true;
		}
        return true;
	}

	public static function handleDir($dir) {
		return is_dir($dir) || self::make_dir($dir);
	}
}
?>
