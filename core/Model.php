<?php
namespace Core;

use Core\Database;

class Model {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
}
