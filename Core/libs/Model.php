<?php 
require_once __DIR__.'/Logger.php';
require_once __DIR__.'/LevelDB.php';
require_once __DIR__.'/ORM/QueryBuilder.php';

use Core\Attributes\Field;

abstract class Model
{
    public $db;
    public $portabeDB;
    protected static $dbConnection = null;
    protected static $tableName = null; // Set in child classes ideally

    public function __construct()
    {
        // Legacy fallback connections for old instances
		try {
			$this->portabeDB = new PortableDB('core.db');
		} catch (PDOException $e) { }

        // Use global static connection if available, otherwise spin up local
        if (self::$dbConnection) {
            $this->db = self::$dbConnection;
        } else {
            try {
                $this->db = new Database(
                    Config::get('DB_TYPE'),
                    Config::get('DB_HOST'),
                    Config::get('DB_NAME'),
                    Config::get('DB_USER'),
                    Config::get('DB_PASS')
                );
                self::$dbConnection = $this->db;
            } catch (PDOException $e) {
                Logger::Error("ERROR INVALID DATABASE CREDENTIALS: " . $e->getMessage() . PHP_EOL);
            }
        }
    }

    public static function setConnection($db) {
        self::$dbConnection = $db;
    }

    public static function getConnection() {
        return self::$dbConnection;
    }
    
    protected static function getTable() {
        if (static::$tableName) {
            return static::$tableName;
        }
        $class = strtolower(basename(str_replace('\\', '/', static::class)));
        return $class . 's'; // simplistic pluralization heuristic
    }

    public static function find(array $conditions = []) {
        if (!self::$dbConnection) {
            throw new Exception("Database connection not initialized. Call Model::setConnection() first.");
        }
        $builder = new QueryBuilder(self::$dbConnection, static::getTable());
        return $builder->find($conditions);
    }

    public static function create(array $data) {
        if (!self::$dbConnection) {
            throw new Exception("Database connection not initialized.");
        }
        
        $validatedData = self::validateData($data);
        self::$dbConnection->insert(static::getTable(), $validatedData);
        
        return (object) $validatedData; 
    }

    protected static function validateData(array $data) {
        $reflectionClass = new ReflectionClass(static::class);
        $validated = [];

        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $attributes = $property->getAttributes(Field::class);
            if (empty($attributes)) {
                continue; // Not an ORM field
            }

            $fieldMeta = $attributes[0]->newInstance();
            $name = $property->getName();

            // Presence validation
            if ($fieldMeta->required && !array_key_exists($name, $data)) {
                throw new Exception("Field '$name' is required.");
            }

            if (array_key_exists($name, $data)) {
                $val = $data[$name];

                // Type assertion
                if ($fieldMeta->type === 'int') {
                    if (is_numeric($val) && (int)$val == $val) {
                         $val = (int)$val;
                    } elseif (!is_int($val)) {
                         throw new Exception("Field '$name' must be of type integer.");
                    }
                } elseif ($fieldMeta->type === 'string' && !is_string($val)) {
                    throw new Exception("Field '$name' must be of type string.");
                }

                // Bounds validation
                if ($fieldMeta->type === 'int') {
                    if ($fieldMeta->min !== null && $val < $fieldMeta->min) {
                        throw new Exception("Field '$name' must be at least {$fieldMeta->min}.");
                    }
                    if ($fieldMeta->max !== null && $val > $fieldMeta->max) {
                        throw new Exception("Field '$name' must be at most {$fieldMeta->max}.");
                    }
                }

                $validated[$name] = $val;
            }
        }
        
        return empty($validated) ? $data : $validated; 
    }
}
?>