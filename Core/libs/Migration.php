<?php
require_once __DIR__ . '/Database.php';
/**
 * Migration Engine with Schema Diffing & ALTER TABLE Support
 * 
 * Scans App/models/ for Model subclasses with #[Field] attributes,
 * introspects the existing database schema, diffs them, and generates
 * CREATE TABLE or ALTER TABLE statements as needed.
 */
class Migration extends Database
{
	private $newMigrations = [];

	public function __construct($db_type, $db_host, $db_name, $db_user, $db_pass)
	{
		parent::__construct($db_type, $db_host, $db_name, $db_user, $db_pass);
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

    // =========================================================================
    //  Schema Sync Entry Point
    // =========================================================================

    private function syncSchemaForModel(string $className) {
        $reflection = new ReflectionClass($className);
        
        // Derive table name
        $tableName = $this->resolveTableName($reflection);

        echo "=> Syncing schema for table: $tableName (Model: {$reflection->getShortName()})\n";

        // Build desired schema from model attributes
        $desiredColumns = $this->buildDesiredSchema($reflection);

        if (!$this->tableExists($tableName)) {
            // --- Fresh CREATE TABLE ---
            $this->createTable($tableName, $desiredColumns);
        } else {
            // --- Diff & ALTER TABLE ---
            $existingColumns = $this->getExistingColumns($tableName);
            $diff = $this->diffSchema($existingColumns, $desiredColumns);
            $this->applyDiff($tableName, $diff);
        }
    }

    // =========================================================================
    //  Table Name Resolution
    // =========================================================================

    private function resolveTableName(ReflectionClass $reflection): string {
        if ($reflection->hasProperty('tableName')) {
            $prop = $reflection->getProperty('tableName');
            $prop->setAccessible(true);
            $tableName = $prop->getValue();
            if ($tableName) {
                return $tableName;
            }
        }
        return strtolower($reflection->getShortName()) . 's';
    }

    // =========================================================================
    //  Schema Introspection (read what the DB currently has)
    // =========================================================================

    /**
     * Check if a table exists in the current database.
     */
    private function tableExists(string $tableName): bool {
        if ($this->_dbType === 'sqlite') {
            $stmt = $this->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=:table");
        } else {
            $stmt = $this->prepare("SHOW TABLES LIKE :table");
        }
        $stmt->execute([':table' => $tableName]);
        return $stmt->fetch() !== false;
    }

    /**
     * Retrieve existing column metadata from the database.
     */
    private function getExistingColumns(string $tableName): array {
        if ($this->_dbType === 'sqlite') {
            $stmt = $this->prepare("PRAGMA table_info(`$tableName`)");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $columns = [];
            foreach ($rows as $row) {
                // SQLite PRAGMA table_info columns: cid, name, type, notnull, dflt_value, pk
                $colName = $row['name'];
                $isUnique = in_array('UNIQUE', $this->getColumnIndexes($tableName, $colName));
                
                $columns[$colName] = [
                    'type'     => strtoupper($row['type']),
                    'nullable' => ($row['notnull'] == 0),
                    'key'      => ($row['pk'] > 0) ? 'PRI' : ($isUnique ? 'UNI' : ''),
                    'default'  => $row['dflt_value'],
                    'extra'    => '', 
                ];
            }
            return $columns;
        } else {
            $stmt = $this->prepare("SHOW COLUMNS FROM `$tableName`");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $columns = [];
            foreach ($rows as $row) {
                $columns[$row['Field']] = [
                    'type'     => strtoupper($row['Type']),
                    'nullable' => ($row['Null'] === 'YES'),
                    'key'      => $row['Key'],
                    'default'  => $row['Default'],
                    'extra'    => $row['Extra'],
                ];
            }
            return $columns;
        }
    }

    // =========================================================================
    //  Build Desired Schema from Model Attributes
    // =========================================================================

    /**
     * Map a Field attribute type string to the MySQL column type.
     */
    private function mapFieldTypeToSQL(string $type): string {
        return match ($type) {
            'int'            => 'INT',
            'boolean', 'bool'=> 'TINYINT(1)',
            'text'           => 'TEXT',
            'date'           => 'DATE',
            'datetime'       => 'DATETIME',
            'float', 'double'=> 'DOUBLE',
            default          => 'VARCHAR(255)',  // 'string' and anything else
        };
    }

    /**
     * Build the desired column definitions from model reflection.
     * Returns the same format as getExistingColumns() for easy comparison.
     * Does NOT include `id` or `created_at` — those are system-managed.
     */
    private function buildDesiredSchema(ReflectionClass $reflection): array {
        $columns = [];

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $attributes = $property->getAttributes(\Core\Attributes\Field::class);
            if (empty($attributes)) continue;

            $fieldMeta = $attributes[0]->newInstance();
            $name = $property->getName();

            $sqlType  = $this->mapFieldTypeToSQL($fieldMeta->type);
            $nullable = !$fieldMeta->required && $fieldMeta->nullable;
            $unique   = $fieldMeta->unique;
            $default  = $fieldMeta->default;

            $columns[$name] = [
                'type'     => $sqlType,
                'nullable' => $nullable,
                'key'      => $unique ? 'UNI' : '',
                'default'  => $default,
                'extra'    => '',
            ];
        }

        return $columns;
    }

    // =========================================================================
    //  Schema Diffing
    // =========================================================================

    /**
     * Compares existing DB columns vs desired model columns.
     * Returns ['add' => [...], 'modify' => [...], 'drop' => [...]]
     * 
     * System columns (id, created_at) are excluded from diffing.
     */
    private function diffSchema(array $existing, array $desired): array {
        $systemColumns = ['id', 'created_at'];

        $diff = [
            'add'    => [],
            'modify' => [],
            'drop'   => [],
        ];

        // Columns to ADD (in desired but not in existing)
        foreach ($desired as $colName => $colDef) {
            if (!isset($existing[$colName])) {
                $diff['add'][$colName] = $colDef;
            }
        }

        // Columns to MODIFY (in both but different)
        foreach ($desired as $colName => $desiredDef) {
            if (isset($existing[$colName])) {
                $existingDef = $existing[$colName];

                // Normalize types for comparison
                $existingType = $this->normalizeType($existingDef['type']);
                $desiredType  = $this->normalizeType($desiredDef['type']);

                $changed = false;

                if ($existingType !== $desiredType) {
                    $changed = true;
                }
                if ($existingDef['nullable'] !== $desiredDef['nullable']) {
                    $changed = true;
                }
                // Compare unique constraint
                $existingUnique = ($existingDef['key'] === 'UNI');
                $desiredUnique  = ($desiredDef['key'] === 'UNI');
                if ($existingUnique !== $desiredUnique) {
                    $changed = true;
                }

                if ($changed) {
                    $diff['modify'][$colName] = $desiredDef;
                }
            }
        }

        // Columns to DROP (in existing but not in desired, excluding system columns)
        foreach ($existing as $colName => $colDef) {
            if (in_array($colName, $systemColumns)) continue;
            if (!isset($desired[$colName])) {
                $diff['drop'][$colName] = $colDef;
            }
        }

        return $diff;
    }

    /**
     * Normalize MySQL type strings for comparison.
     * e.g. 'INT(11)' -> 'INT', 'VARCHAR(255)' -> 'VARCHAR(255)'
     */
    private function normalizeType(string $type): string {
        $type = strtoupper(trim($type));
        // INT(11) -> INT  (MySQL default display width, not meaningful after 8.0)
        if (preg_match('/^INT\(\d+\)$/', $type)) {
            return 'INT';
        }
        // TINYINT(1) stays as-is (used for booleans)
        return $type;
    }

    // =========================================================================
    //  Apply Diff (Generate & Execute ALTER TABLE)
    // =========================================================================

    private function applyDiff(string $tableName, array $diff): void {
        if ($this->_dbType === 'sqlite') {
            $this->applyDiffSqlite($tableName, $diff);
        } else {
            $this->applyDiffMysql($tableName, $diff);
        }
    }

    private function applyDiffMysql(string $tableName, array $diff): void {
        $hasChanges = false;

        // --- ADD columns ---
        foreach ($diff['add'] as $colName => $colDef) {
            $sql = $this->buildColumnSQL($colName, $colDef);
            $alterSQL = "ALTER TABLE `$tableName` ADD COLUMN $sql";
            $this->executeAlter($tableName, $alterSQL, "ADD COLUMN `$colName`");
            $hasChanges = true;

            // If unique, add the unique index separately
            if ($colDef['key'] === 'UNI') {
                $indexSQL = "ALTER TABLE `$tableName` ADD UNIQUE INDEX `uq_{$tableName}_{$colName}` (`$colName`)";
                $this->executeAlter($tableName, $indexSQL, "ADD UNIQUE INDEX on `$colName`");
            }
        }

        // --- MODIFY columns ---
        foreach ($diff['modify'] as $colName => $colDef) {
            $sql = $this->buildColumnSQL($colName, $colDef);
            $alterSQL = "ALTER TABLE `$tableName` MODIFY COLUMN $sql";
            $this->executeAlter($tableName, $alterSQL, "MODIFY COLUMN `$colName`");
            $hasChanges = true;

            // Handle unique constraint changes
            $desiredUnique = ($colDef['key'] === 'UNI');
            $existingIndexes = $this->getColumnIndexes($tableName, $colName);
            $hasUniqueIndex = in_array('UNIQUE', $existingIndexes);

            if ($desiredUnique && !$hasUniqueIndex) {
                $indexSQL = "ALTER TABLE `$tableName` ADD UNIQUE INDEX `uq_{$tableName}_{$colName}` (`$colName`)";
                $this->executeAlter($tableName, $indexSQL, "ADD UNIQUE INDEX on `$colName`");
            } elseif (!$desiredUnique && $hasUniqueIndex) {
                $indexName = $this->findUniqueIndexName($tableName, $colName);
                if ($indexName) {
                    $dropIdxSQL = "ALTER TABLE `$tableName` DROP INDEX `$indexName`";
                    $this->executeAlter($tableName, $dropIdxSQL, "DROP UNIQUE INDEX on `$colName`");
                }
            }
        }

        // --- DROP columns ---
        if (!empty($diff['drop'])) {
            echo "\n   [WARNING] The following columns exist in the database but NOT in the model:\n";
            foreach ($diff['drop'] as $colName => $colDef) {
                echo "     - `$colName` ({$colDef['type']})\n";
            }

            echo "   Drop these columns? (y/N): ";
            $answer = trim(fgets(STDIN));

            if (strtolower($answer) === 'y') {
                foreach ($diff['drop'] as $colName => $colDef) {
                    $alterSQL = "ALTER TABLE `$tableName` DROP COLUMN `$colName`";
                    $this->executeAlter($tableName, $alterSQL, "DROP COLUMN `$colName`");
                    $hasChanges = true;
                }
            }
        }

        if (!$hasChanges) {
            echo "   [OK] Table `$tableName` is already in sync.\n";
        }
    }

    private function applyDiffSqlite(string $tableName, array $diff): void {
        // SQLite only supports ALTER TABLE ADD COLUMN.
        // MODIFY and DROP require recreating the table.
        if (empty($diff['modify']) && empty($diff['drop'])) {
            // Only additions
            foreach ($diff['add'] as $colName => $colDef) {
                $sql = $this->buildColumnSQL($colName, $colDef);
                if ($colDef['key'] === 'UNI') {
                    $sql .= ' UNIQUE';
                }
                $this->exec("ALTER TABLE `$tableName` ADD COLUMN $sql");
                echo "   [SUCCESS] $tableName -> ADD COLUMN `$colName` (SQLite)\n";
            }
            if (empty($diff['add'])) {
                echo "   [OK] Table `$tableName` is already in sync.\n";
            }
        } else {
            // Complex change: Recreate table
            echo "   [INFO] Recreating SQLite table `$tableName` to apply modifications/drops...\n";
            
            $existingColumns = array_keys($this->getExistingColumns($tableName));
            $desiredColumnsForCreation = $this->buildDesiredSchema(new ReflectionClass($this->getCurrentModelClass($tableName)));
            
            $tempTable = $tableName . '_temp';
            $this->createTable($tempTable, $desiredColumnsForCreation);
            
            // Intersection of columns to copy
            $newColumns = array_keys($this->getExistingColumns($tempTable));
            $commonColumns = array_intersect($existingColumns, $newColumns);
            $colList = implode(', ', array_map(fn($c) => "`$c`", $commonColumns));
            
            $this->exec("INSERT INTO `$tempTable` ($colList) SELECT $colList FROM `$tableName` ");
            $this->exec("DROP TABLE `$tableName` ");
            $this->exec("ALTER TABLE `$tempTable` RENAME TO `$tableName` ");
            
            echo "   [SUCCESS] $tableName -> Refactored table structure (SQLite)\n";
        }
    }

    private function getCurrentModelClass(string $tableName): ?string {
        foreach (get_declared_classes() as $className) {
            if (is_subclass_of($className, 'Model')) {
                // Simplified check - assumes pluralization as done in resolveTableName
                if ($this->resolveTableName(new ReflectionClass($className)) === $tableName) {
                    return $className;
                }
            }
        }
        return null;
    }

    /**
     * Build a column definition SQL fragment: `name` TYPE [NOT NULL] [DEFAULT x]
     */
    private function buildColumnSQL(string $colName, array $colDef): string {
        $parts = ["`$colName`", $colDef['type']];

        if (!$colDef['nullable']) {
            $parts[] = 'NOT NULL';
        } else {
            $parts[] = 'NULL';
        }

        if ($colDef['default'] !== null) {
            $defaultVal = is_string($colDef['default']) 
                ? "'" . addslashes($colDef['default']) . "'" 
                : $colDef['default'];
            $parts[] = "DEFAULT $defaultVal";
        }

        return implode(' ', $parts);
    }

    /**
     * Execute an ALTER statement with error handling and output.
     */
    private function executeAlter(string $tableName, string $sql, string $description): void {
        try {
            $this->exec($sql);
            echo "   [SUCCESS] $tableName -> $description\n";
        } catch (PDOException $e) {
            echo "   [ERROR] $tableName -> $description: " . $e->getMessage() . "\n";
        }
    }

    // =========================================================================
    //  Index Helpers
    // =========================================================================

    /**
     * Get the types of indexes on a specific column.
     */
    private function getColumnIndexes(string $tableName, string $colName): array {
        $types = [];
        if ($this->_dbType === 'sqlite') {
            $stmt = $this->prepare("PRAGMA index_list(`$tableName`)");
            $stmt->execute();
            $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($indexes as $idx) {
                $stmt = $this->prepare("PRAGMA index_info(`" . $idx['name'] . "`)");
                $stmt->execute();
                $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($cols as $col) {
                    if ($col['name'] === $colName) {
                        $types[] = ($idx['unique'] == 1) ? 'UNIQUE' : 'INDEX';
                    }
                }
            }
        } else {
            $stmt = $this->prepare("SHOW INDEX FROM `$tableName` WHERE Column_name = :col");
            $stmt->execute([':col' => $colName]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                if ($row['Non_unique'] == 0) {
                    $types[] = 'UNIQUE';
                } else {
                    $types[] = 'INDEX';
                }
            }
        }
        return $types;
    }

    /**
     * Find the name of a unique index on a specific column.
     */
    private function findUniqueIndexName(string $tableName, string $colName): ?string {
        if ($this->_dbType === 'sqlite') {
            $stmt = $this->prepare("PRAGMA index_list(`$tableName`)");
            $stmt->execute();
            $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($indexes as $idx) {
                if ($idx['unique'] == 1) {
                    $stmt = $this->prepare("PRAGMA index_info(`" . $idx['name'] . "`)");
                    $stmt->execute();
                    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($cols as $col) {
                        if ($col['name'] === $colName) {
                            return $idx['name'];
                        }
                    }
                }
            }
        } else {
            $stmt = $this->prepare("SHOW INDEX FROM `$tableName` WHERE Column_name = :col AND Non_unique = 0");
            $stmt->execute([':col' => $colName]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && $row['Key_name'] !== 'PRIMARY') {
                return $row['Key_name'];
            }
        }
        return null;
    }

    // =========================================================================
    //  Fresh Table Creation
    // =========================================================================

    private function createTable(string $tableName, array $desiredColumns): void {
        $columnDefs = [];
        if ($this->_dbType === 'sqlite') {
            $columnDefs[] = "`id` INTEGER PRIMARY KEY AUTOINCREMENT";
        } else {
            $columnDefs[] = "`id` INT AUTO_INCREMENT PRIMARY KEY";
        }

        foreach ($desiredColumns as $colName => $colDef) {
            $line = $this->buildColumnSQL($colName, $colDef);
            if ($colDef['key'] === 'UNI') {
                $line .= ' UNIQUE';
            }
            $columnDefs[] = $line;
        }

        // System timestamp column
        $columnDefs[] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";

        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (\n  " 
             . implode(",\n  ", $columnDefs) 
             . "\n)";
        
        if ($this->_dbType !== 'sqlite') {
             $sql .= " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        } else {
             $sql .= ";";
        }

        try {
            $this->exec($sql);
            echo "   [SUCCESS] Created table `$tableName`\n";
        } catch (PDOException $e) {
            echo "   [ERROR] Failed to create `$tableName`: " . $e->getMessage() . "\n";
        }
    }
}


 ?>