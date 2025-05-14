<?php
session_start();
require_once "../Database.php";
$config = require("../config.php");
$db = new Database($config['database']);

// =====================
// Handle Add Product
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $image = null;

    if ($name && $price) {
        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadDir = '../pic/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $imageName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $imageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $image = $targetPath;
            }
        }

        $stmt = $db->prepare("INSERT INTO products (name, price, description, image, stock) 
                              VALUES (:name, :price, :description, :image, :stock)");
        $stmt->execute([
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'image' => $image,
            'stock' => $stock
        ]);

        header("Location: products.php");
        exit;
    }
}

// =====================
// Handle Update Product
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $image = $_POST['current_image'] ?? '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = '../pic/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            if ($image && file_exists($image)) {
                unlink($image);
            }
            $image = $targetPath;
        }
    }

    $stmt = $db->prepare("UPDATE products SET name = :name, price = :price, description = :description, image = :image, stock = :stock WHERE id = :id");
    $stmt->execute([
        'id' => $id,
        'name' => $name,
        'price' => $price,
        'description' => $description,
        'image' => $image,
        'stock' => $stock
    ]);

    header("Location: products.php");
    exit;
}

// =====================
// Handle Delete Product
// =====================
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $db->prepare("SELECT image FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();
    if ($product && !empty($product['image']) && file_exists($product['image'])) {
        unlink($product['image']);
    }

    $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: products.php");
    exit;
}

// =====================
// Get product to edit (if any)
// =====================
$editingProduct = null;
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $editId]);
    $editingProduct = $stmt->fetch();
}

// =====================
// Fetch all products
// =====================
$products = $db->query("SELECT * FROM products ORDER BY created_at ASC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php require("views/sidebar.php"); ?>
<div class="container mt-5 d-flex justify-content-end">
    <div class="w-75">
        <h2 class="mb-4">Product Management</h2>

        <!-- Add/Edit Product Form -->
        <form method="POST" enctype="multipart/form-data" class="card p-3 mb-4 bg-light">
            <h5><?= $editingProduct ? 'Edit Product' : 'Add New Product' ?></h5>

            <?php if ($editingProduct): ?>
                <input type="hidden" name="id" value="<?= $editingProduct['id'] ?>">
                <input type="hidden" name="current_image" value="<?= $editingProduct['image'] ?>">
            <?php endif; ?>

            <div class="mb-2">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required 
                       value="<?= $editingProduct['name'] ?? '' ?>">
            </div>
            <div class="mb-2">
                <label>Price (₦)</label>
                <input type="number" name="price" step="0.01" class="form-control" required
                       value="<?= $editingProduct['price'] ?? '' ?>">
            </div>
            <div class="mb-2">
                <label>Description</label>
                <textarea name="description" class="form-control"><?= $editingProduct['description'] ?? '' ?></textarea>
            </div>
            <div class="mb-2">
                <label>Stock Quantity</label>
                <input type="number" name="stock" class="form-control" required min="0"
                       value="<?= $editingProduct['stock'] ?? 0 ?>">
            </div>
            <div class="mb-2">
                <label>Product Image</label>
                <?php if ($editingProduct && !empty($editingProduct['image'])): ?>
                    <div class="mb-2">
                        <img src="<?= htmlspecialchars($editingProduct['image']) ?>" width="80" alt="Current Image">
                    </div>
                <?php endif; ?>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <button type="submit" name="<?= $editingProduct ? 'update_product' : 'add_product' ?>" 
                    class="btn btn-<?= $editingProduct ? 'warning' : 'primary' ?>">
                <?= $editingProduct ? 'Update Product' : 'Add Product' ?>
            </button>

            <?php if ($editingProduct): ?>
                <a href="products.php" class="btn btn-secondary ms-2">Cancel</a>
            <?php endif; ?>
        </form>

        <!-- Product Table -->
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th><th>Name</th><th>Price</th><th>Description</th><th>Image</th><th>Created</th><th>Stock</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td>₦<?= number_format($product['price'], 2) ?></td>
                    <td><?= nl2br(htmlspecialchars($product['description'])) ?></td>
                    <td>
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?= htmlspecialchars($product['image']) ?>" width="60" alt="Product Image">
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?= $product['created_at'] ?></td>
                    <td>
                        <?php if ($product['stock'] > 0): ?>
                            <span class="badge bg-success">In Stock (<?= $product['stock'] ?>)</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Out of Stock</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="products.php?edit=<?= $product['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="products.php?delete=<?= $product['id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Are you sure you want to delete this product?');">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
