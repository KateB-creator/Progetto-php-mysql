<?php

require_once __DIR__ . '/../models/Product.php';

class ProductController {
    private $product;

    public function __construct($db) {
        $this->product = new Product($db);
    }

    // CREATE (già implementato)
    public function create() {
        $_POST = json_decode(file_get_contents('php://input'), true);
        if (!isset($_POST['name']) || !isset($_POST['kg_recycled'])) {
            http_response_code(400);
            echo json_encode(["message" => "Parametri mancanti"]);
            return;
        }
        if ($this->product->create($_POST['name'], $_POST['kg_recycled'])) {
            http_response_code(201);
            echo json_encode(["message" => "Prodotto creato correttamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Errore nella creazione del prodotto"]);
        }
    }

    // GET ALL
    public function getAll() {
        $products = $this->product->getAll();
        http_response_code(200);
        echo json_encode($products);
    }

    // GET BY ID
    public function getById($id) {
        $product = $this->product->getById($id);
        if ($product) {
            http_response_code(200);
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Prodotto non trovato"]);
        }
    }

    // UPDATE
    public function update($id) {
        $_PUT = json_decode(file_get_contents('php://input'), true);
        if (!isset($_PUT['name']) || !isset($_PUT['kg_recycled'])) {
            http_response_code(400);
            echo json_encode(["message" => "Parametri mancanti"]);
            return;
        }
        if ($this->product->update($id, $_PUT['name'], $_PUT['kg_recycled'])) {
            http_response_code(200);
            echo json_encode(["message" => "Prodotto aggiornato"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Errore aggiornamento prodotto"]);
        }
    }

    // DELETE
    public function delete($id) {
        if ($this->product->delete($id)) {
            http_response_code(200);
            echo json_encode(["message" => "Prodotto eliminato"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Errore eliminazione prodotto"]);
        }
    }
}


