<?php

class DashboardController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getRecycledPlastic() {
        $params = $_GET;
        $start_date = isset($params['start_date']) ? $params['start_date'] : null;
        $end_date = isset($params['end_date']) ? $params['end_date'] : null;
        $product_id = isset($params['product_id']) ? $params['product_id'] : null;

        $query = "SELECT SUM(p.kg_recycled * oi.quantity) AS total_kg
                  FROM order_items oi
                  JOIN products p ON oi.product_id = p.id
                  JOIN orders o ON oi.order_id = o.id
                  WHERE 1=1";

        if ($start_date) {
            $query .= " AND o.order_date >= :start_date";
        }
        if ($end_date) {
            $query .= " AND o.order_date <= :end_date";
        }
        if ($product_id) {
            $query .= " AND p.id = :product_id";
        }

        $stmt = $this->conn->prepare($query);

        if ($start_date) {
            $stmt->bindParam(':start_date', $start_date);
        }
        if ($end_date) {
            $stmt->bindParam(':end_date', $end_date);
        }
        if ($product_id) {
            $stmt->bindParam(':product_id', $product_id);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([
            "total_kg_recycled" => $result['total_kg'] ?? 0
        ]);
    }
}
