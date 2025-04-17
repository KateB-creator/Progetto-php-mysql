<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/OrderController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$db = (new Database())->getConnection();
$productController = new ProductController($db);
$orderController = new OrderController($db);
$dashboardController = new DashboardController($db);

// Debug (facoltativo)
// error_log("DEBUG URI: " . $uri);

// Estrai eventuale ID prodotto
$id = null;
if (preg_match('/\/api\/products\/(\d+)/', $uri, $matches)) {
    $id = (int)$matches[1];
}

// Estrai eventuale ID ordine
$order_id = null;
if (preg_match('/\/api\/orders\/(\d+)/', $uri, $matches)) {
    $order_id = (int)$matches[1];
}

switch (true) {
    // --- PRODUCTS ---
    case $method === 'GET' && preg_match('/\/index\.php\/api\/products$/', $uri):
        $productController->getAll();
        break;

    case $method === 'GET' && preg_match('/\/index\.php\/api\/products\/(\d+)$/', $uri):
        $productController->getById($id);
        break;

    case $method === 'POST' && preg_match('/\/index\.php\/api\/products$/', $uri):
        $productController->create();
        break;

    case $method === 'PUT' && preg_match('/\/index\.php\/api\/products\/(\d+)$/', $uri):
        $productController->update($id);
        break;

    case $method === 'DELETE' && preg_match('/\/index\.php\/api\/products\/(\d+)$/', $uri):
        $productController->delete($id);
        break;

    // --- ORDERS ---
    case $method === 'GET' && preg_match('/\/index\.php\/api\/orders$/', $uri):
        $orderController->getAll();
        break;

    case $method === 'GET' && preg_match('/\/index\.php\/api\/orders\/(\d+)$/', $uri):
        $orderController->getById($order_id);
        break;

    case $method === 'POST' && preg_match('/\/index\.php\/api\/orders$/', $uri):
        $orderController->create();
        break;

    case $method === 'PUT' && preg_match('/\/index\.php\/api\/orders\/(\d+)$/', $uri):
        $orderController->update($order_id);
        break;

    case $method === 'DELETE' && preg_match('/\/index\.php\/api\/orders\/(\d+)$/', $uri):
        $orderController->delete($order_id);
        break;

    // --- DASHBOARD ---
    case $method === 'GET' && preg_match('/\/index\.php\/api\/recycled-plastic(\?.*)?$/', $uri):
        $dashboardController->getRecycledPlastic();
        break;

    // --- DEFAULT ---
    default:
        http_response_code(404);
        echo json_encode(["message" => "Endpoint non trovato"]);
        break;
}
