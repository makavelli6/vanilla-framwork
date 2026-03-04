<?php
class Text {
	
	public static function write_file($file, $txt = '') {
		$handle = @fopen($file, "w");
        if (!$handle) return false;
        
        if (flock($handle, LOCK_EX)) {
            $result = fwrite($handle, $txt);
            flock($handle, LOCK_UN);
            fclose($handle);
            return $result !== false;
        }
		fclose($handle);
        return false;
	}

	public static function write_file_plus($file, $txt) {
		$handle = @fopen($file, "w+");
        if (!$handle) return false;
        
        if (flock($handle, LOCK_EX)) {
		    $result = fwrite($handle, $txt);
            flock($handle, LOCK_UN);
		    fclose($handle);
            return $result !== false;
        }
        fclose($handle);
        return false;
	}

	public static function read_file_plus($file) {
        if (!file_exists($file)) return false;
		$handle = @fopen($file, "r+");
        if (!$handle) return false;

        $content = false;
        if (flock($handle, LOCK_SH)) {
            $size = filesize($file);
            if ($size > 0) {
		        $content = fread($handle, $size);
            } else {
                $content = "";
            }
            flock($handle, LOCK_UN);
        }
		fclose($handle);
		return $content;
	}

	public static function read_file_plus_array($file) {
        if (!file_exists($file)) return false;
		$handle = @fopen($file, "r+");
        if (!$handle) return false;
        
        $data = [];
        if (flock($handle, LOCK_SH)) {
		    while(!feof($handle)) {
			    $line = fgets($handle);
                if ($line !== false) {
                    $data[] = $line;
                }
		    }
            flock($handle, LOCK_UN);
        }
		fclose($handle);
		return empty($data) ? "" : implode("", $data);
	}

	public static function read_file($file) {
        if (!file_exists($file)) return false;
		$handle = @fopen($file, "r");
        if (!$handle) return false;
        
        $content = false;
        if (flock($handle, LOCK_SH)) {
            $size = filesize($file);
            if ($size > 0) {
		        $content = fread($handle, $size);
            } else {
                $content = "";
            }
            flock($handle, LOCK_UN);
        }
		fclose($handle);
		return $content;
	}

	public static function read_file_array($file) {
        if (!file_exists($file)) return false;
		$handle = @fopen($file, "r");
        if (!$handle) return false;

        $data = [];
        if (flock($handle, LOCK_SH)) {
		    while(!feof($handle)) {
			    $line = fgets($handle);
                if ($line !== false) {
                    $data[] = $line;
                }
		    }
            flock($handle, LOCK_UN);
        }
		fclose($handle);
		return empty($data) ? "" : implode("", $data);
	}

	public static function create_file($file) {
		$handle = @fopen($file, "x");
		if ($handle === false) {
			return false;
		}
		fclose($handle);
        return true;
	}

	public static function create_file_plus($file) {
		$handle = @fopen($file, "x+");
		if ($handle === false) {
			return false;
		}
		fclose($handle);
        return true;
	}

	public static function append_file($file, $txt) {
		$handle = @fopen($file, "a");
        if (!$handle) return false;

        if (flock($handle, LOCK_EX)) {
		    $result = fwrite($handle, $txt);
            flock($handle, LOCK_UN);
		    fclose($handle);
            return $result !== false;
        }
        fclose($handle);
        return false;
	}

	public static function append_file_plus($file, $txt) {
		$handle = @fopen($file, "a+");
        if (!$handle) return false;

        if (flock($handle, LOCK_EX)) {
		    $result = fwrite($handle, $txt);
            flock($handle, LOCK_UN);
		    fclose($handle);
            return $result !== false;
        }
        fclose($handle);
        return false;
	}
}
?>