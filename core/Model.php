<?php

namespace Core;

use Core\Database;
use PDO;

class Model
{
    protected $db;
    protected $table;

    public function __construct($table)
    {
        $this->db = Database::getInstance();
        $this->table = $table;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM " . $this->table;
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO " . $this->table . " (" . $columns . ") VALUES (" . $placeholders . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE " . $this->table . " SET ";
        $update_fields = [];
        foreach ($data as $key => $value) {
            $update_fields[] = "$key = :$key";
        }
        $sql .= implode(", ", $update_fields) . " WHERE id = ?";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
