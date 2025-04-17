<?php

class Product {
    private $conn;
    private $table = 'products';

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE (già implementato)
    public function create($name, $kg_recycled) {
        $query = "INSERT INTO {$this->table} (name, kg_recycled) VALUES (:name, :kg_recycled)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':kg_recycled', $kg_recycled);
        return $stmt->execute();
    }

    // READ (tutti i prodotti)
    public function getAll() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ (singolo prodotto per id)
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE (modifica prodotto)
    public function update($id, $name, $kg_recycled) {
        $query = "UPDATE {$this->table} SET name = :name, kg_recycled = :kg_recycled WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':kg_recycled', $kg_recycled);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // DELETE (elimina prodotto)
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
