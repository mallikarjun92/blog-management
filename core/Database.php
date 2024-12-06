<?php
namespace Core;

use PDO;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $config = require __DIR__ . '/../config.php';
        
        $this->connection = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['dbname']}", $config['db']['user'], $config['db']['password']);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }
}
