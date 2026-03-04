<?php

abstract class Service
{
    /**
     * @var Database
     */
    protected $db;

    public function __construct()
    {
        // Try getting the global ORM connection first
        if (class_exists('Model') && Model::getConnection()) {
            $this->db = Model::getConnection();
        } else {
            // Fallback standalone connection
            $this->db = new Database(
                Config::get('DB_TYPE'),
                Config::get('DB_HOST'),
                Config::get('DB_NAME'),
                Config::get('DB_USER'),
                Config::get('DB_PASS')
            );
        }
    }
}
?>
