<?php
session_start();
require_once "../Database.php";
$config = require("../config.php");
$db = new Database($config['database']);

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: products.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Product not found.";
    exit;
}

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';
    $stock = $_POST['stock'] ?? 0;

    $stmt = $db->prepare("UPDATE products 
                          SET name = :name, price = :price, description = :description, stock = :stock 
                          WHERE id = :id");
    $stmt->execute([
        'name' => $name,
        'price' => $price,
        'description' => $description,
        'stock' => $stock,
        'id' => $id
    ]);

    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Product</h2>
    <form method="POST" class="card p-4 bg-light">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Price (₦)</label>
            <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" value="<?= $product['stock'] ?>" class="form-control" min="0" required>
        </div>
        <button type="submit" class="btn btn-success">Update Product</button>
        <a href="products.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
