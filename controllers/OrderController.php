<?php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';

class OrderController {
    private $order;
    private $orderItem;
    private $conn;

    public function __construct($db) {
        $this->order = new Order($db);
        $this->orderItem = new OrderItem($db);
        $this->conn = $db;
    }

    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['order_date']) || !isset($data['items']) || !is_array($data['items'])) {
            http_response_code(400);
            echo json_encode(["message" => "Parametri ordine mancanti o errati"]);
            return;
        }

        try {
            $orderId = $this->order->create($data['order_date']);

            foreach ($data['items'] as $item) {
                $this->orderItem->create($orderId, $item['product_id'], $item['quantity']);
            }

            http_response_code(201);
            echo json_encode(["message" => "Ordine creato correttamente", "order_id" => $orderId]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Errore creazione ordine: " . $e->getMessage()]);
        }
    }

    public function getById($id) {
        $order = $this->order->getById($id);
        if (!$order) {
            http_response_code(404);
            echo json_encode(["message" => "Ordine non trovato"]);
            return;
        }

        $order['items'] = $this->orderItem->getItemsByOrderId($id);
        http_response_code(200);
        echo json_encode($order);
    }

    public function getAll() {
        $orders = $this->order->getAll();
        foreach ($orders as &$order) {
            $order['items'] = $this->orderItem->getItemsByOrderId($order['id']);
        }
        echo json_encode($orders);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM orders WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Ordine eliminato con successo"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Errore eliminazione ordine"]);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $date = $data['order_date'] ?? null;

        if (!$date) {
            http_response_code(400);
            echo json_encode(["message" => "Data ordine mancante"]);
            return;
        }

        $stmt = $this->conn->prepare("UPDATE orders SET order_date = :date WHERE id = :id");
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Ordine aggiornato con successo"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Errore aggiornamento"]);
        }
    }
}
