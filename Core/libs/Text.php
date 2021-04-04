<?php
require_once __DIR__.'/File.php';
/**
 * 
 */
class Text extends File
{
	
	public  function write_file($file,$txt = '')
	{
		// w	Open a file for write only.
		// Erases the contents of the file or creates a new file if it doesn't exist.
		// File pointer starts at the beginning of the file
		$handle = fopen($file, "w");
		fwrite($handle, $txt);
		fclose($handle);
	}
	public static function write_file_plus($file,$txt)
	{
		// w+	Open a file for read/write. 
		// Erases the contents of the file or creates a new file if it doesn't exist. 
		// File pointer starts at the beginning of the file
		$handle = fopen($file, "w+")or die("Unable to open file!");
		fwrite($handle, $txt);
		fclose($handle);
	}
	public static function read_file_plus($file)
	{
		// r+	Open a file for read/write. 
		// File pointer starts at the beginning of the file
		//$content = fread($fp, filesize($filename));
		$handle = fopen($file, "r+");
		$content =fread($handle,filesize($file));
		fclose($handle);
		return $content;
	}
		public function read_file_plus_array($file)
	{
		// r+	Open a file for read/write. 
		// File pointer starts at the beginning of the file
		$handle = fopen($file, "r+");
		while(!feof($handle)) {
			$data = fgets($handle);
		}
		fclose($handle);
		return $data;
	}
		public static function read_file($file)
	{
		// r+	Open a file for read/write. 
		// File pointer starts at the beginning of the file
		$handle =fopen($file, "r");
		$content = fread($handle,filesize($file));
		fclose($handle);
		return $content;
		
	}
		public function read_file_array($file)
	{
		// r+	Open a file for read/write. 
		// File pointer starts at the beginning of the file
		fopen($file, "r");
		while(!feof($file)) {
			$data = fgets($file);
		}
		fclose($file);
		return $content;
	}



	public static function create_file($file)
	{
		// x	Creates a new file for write only. 
		//Returns FALSE and an error if file already exists
		$handle =fopen($file, "x");
		if ($handle==FALSE) {
			#file all ready exits
			return 0;
		}
		fclose($handle);
		
	}
	public static function create_file_plus($file)
	{
		// x+	Creates a new file for read/write. 
		// Returns FALSE and an error if file already exists
		$handle = fopen($file, "x+");
		if ($handle==FALSE) {
			#file all ready exits
			return 0;
		}
		fclose($handle);
	}
	public function append_file($file,$txt)
	{
		// a	Open a file for write only. 
		// The existing data in file is preserved. 
		//File pointer starts at the end of the file. 
		//Creates a new file if the file doesn't exist
		$handle = fopen($file, "a");
		fwrite($handle, $txt);
		fclose($handle);
	}
	public function append_file_plus($file,$txt)
	{
		// a+	Open a file for read/write. 
		// The existing data in file is preserved. 
		//File pointer starts at the end of the file. 
		//Creates a new file if the file doesn't exist.
		$handle = fopen($file, "a+");
		fwrite($handle, $txt);
		fclose($handle);
	}

}

 ?>