<?php
session_start();

// Return JSON response
header('Content-Type: application/json');

// Ensure data is sent via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate input
$productId = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? null;

if (!is_numeric($productId) || !is_numeric($quantity) || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Update quantity in session cart
$productId = (int)$productId;
$quantity = (int)$quantity;

$updated = false;

if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as &$item) {
        if ((int)$item['id'] === $productId) {
            $item['quantity'] = $quantity;
            $updated = true;
            break;
        }
    }
}

echo json_encode([
    'success' => $updated,
    'message' => $updated ? 'Quantity updated' : 'Product not found'
]);
