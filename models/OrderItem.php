<?php

class OrderItem {
    private $conn;
    private $table = 'order_items';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crea un elemento d’ordine (riga ordine)
    public function create($order_id, $product_id, $quantity) {
        $query = "INSERT INTO {$this->table} (order_id, product_id, quantity) 
                  VALUES (:order_id, :product_id, :quantity)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        return $stmt->execute();
    }

    // Recupera tutti i prodotti di un ordine
    public function getItemsByOrderId($order_id) {
        $query = "SELECT * FROM {$this->table} WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
