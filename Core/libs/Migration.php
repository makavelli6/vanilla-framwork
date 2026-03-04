<?php 
require_once __DIR__.'/Logger.php';
/**
 * 
 */
class Migration extends PDO
{
	private $newMigrations = [];
	private $_dbName = '';
	public function __construct($db_type,$db_host,$db_name,$db_user,$db_pas)
	{
		parent::__construct($db_type.':host='.$db_host.';dbname='.$db_name,$db_user,$db_pas);
		$this->_dbName = $db_name;
	}

	
	public function applyMigration($basePath)
	{
        if (php_sapi_name() !== 'cli') {
            throw new Exception("Migrations can only be run from the Command Line Interface.");
        }

        $modelsDir = rtrim($basePath, '/') . '/App/models';
        if (!is_dir($modelsDir)) {
            throw new Exception("Model directory does not exist: $modelsDir");
        }

        echo "Scanning models in: $modelsDir \n";
        
        // Ensure core classes are loaded for reflection
        require_once rtrim($basePath, '/') . '/Core/libs/Model.php';
        require_once rtrim($basePath, '/') . '/Core/libs/Attributes/Field.php';
        
        $files = $this->getPhpFiles($modelsDir);
        $syncCount = 0;

        foreach ($files as $file) {
            require_once $file;
        }

        foreach (get_declared_classes() as $className) {
            if (is_subclass_of($className, 'Model')) {
                $this->syncSchemaForModel($className);
                $syncCount++;
            }
        }

        echo "---* Schema Synchronization Complete ($syncCount Models Synced) *---" . PHP_EOL . PHP_EOL;
	}

    private function getPhpFiles($dir) {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }

    private function syncSchemaForModel(string $className) {
        $reflection = new ReflectionClass($className);
        
        // Derive table name. Fallback to lowercase class name + 's' if not defined static prop
        $tableName = null;
        if ($reflection->hasProperty('tableName')) {
            $prop = $reflection->getProperty('tableName');
            $prop->setAccessible(true);
            $tableName = $prop->getValue();
        }
        
        if (!$tableName) {
            $tableName = strtolower($reflection->getShortName()) . 's';
        }

        echo "=> Syncing schema for table: $tableName (Model: {$reflection->getShortName()})\n";

        $columns = [
            "id INT AUTO_INCREMENT PRIMARY KEY"
        ];

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $attributes = $property->getAttributes(\Core\Attributes\Field::class);
            if (empty($attributes)) continue;

            $fieldMeta = $attributes[0]->newInstance();
            $name = $property->getName();

            $sqlType = "VARCHAR(255)"; // Default
            if ($fieldMeta->type === 'int') {
                $sqlType = "INT";
            } else if ($fieldMeta->type === 'boolean' || $fieldMeta->type === 'bool') {
                $sqlType = "TINYINT(1)";
            } else if ($fieldMeta->type === 'text') {
                $sqlType = "TEXT";
            }

            $constraints = [];
            if ($fieldMeta->required) {
                $constraints[] = "NOT NULL";
            }
            if ($fieldMeta->unique) {
                $constraints[] = "UNIQUE";
            }

            $columnDef = "`$name` $sqlType " . implode(' ', $constraints);
            $columns[] = trim($columnDef);
        }

        // We also generally want created_at default timestamps
        $columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";

        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (\n  " . implode(",\n  ", $columns) . "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        try {
            $this->exec($sql);
            echo "   [SUCCESS] Created/Verified table $tableName\n";
        } catch (PDOException $e) {
            echo "   [ERROR] Failed to sync $tableName: " . $e->getMessage() . "\n";
        }
    }

	public function clearMigration()
	{
		$this->dropDB();
		$this->createDB();
	}
	public function refreshMigration($base)
	{
		$this->clearMigration();
		$this->applyMigration($base);
	}
	private function dropDB()
	{
		$this->exec("DROP DATABASE IF EXISTS ".$this->_dbName.";");
	}
	private function createDB()
	{
		$this->exec("CREATE DATABASE ".$this->_dbName);
	}
}


 ?>