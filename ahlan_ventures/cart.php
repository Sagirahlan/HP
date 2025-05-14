<?php
require "functions.php";
require_once "Database.php";
$config = require("config.php");

$db = new Database($config['database']);
session_start();

$errors = [];
$hasError = false;

// Check stock availability for cart items
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as &$item) {
        $productId = $item['id'];
        $qty = intval($item['quantity'] ?? 1);

        $stmt = $db->prepare("SELECT stock FROM products WHERE id = :id");
        $stmt->execute(['id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $errors[] = "Product ID {$productId} not found.";
            $hasError = true;
        } elseif ($product['stock'] < $qty) {
            $errors[] = "{$item['name']} has insufficient stock (Available: {$product['stock']}, Needed: $qty).";
            $hasError = true;
        }
    }
    unset($item);
}

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = 4; // Static for now
    $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $product['quantity'] = 1;
        $_SESSION['cart'][] = $product;
        $success[] = "Product added to cart!";
    }
}

// Remove from cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $cartItem) {
            if ($cartItem['id'] == $_POST['product_id']) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                $success[] = "Product removed from cart!";
                break;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cart</title>
  <?php require("views/partials/head.php"); ?>
</head>
<body>
<?php require("views/partials/nav.php"); ?>

<div class="container mt-5">
  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $error): ?>
        <div><?= htmlspecialchars($error) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php
  if (!empty($_SESSION['cart'])) {
    echo '<h2 class="text-center mb-4">CART</h2>';
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $cartItem) {
      $id = htmlspecialchars($cartItem['id']);
      $name = htmlspecialchars($cartItem['name'] ?? '');
      $price = $cartItem['price'];
      $qty = $cartItem['quantity'] ?? 1;
      $image = htmlspecialchars($cartItem['image'] ?? '');

      echo '
      <div class="d-flex align-items-center border-bottom py-2">
        <div class="me-3">
          <img src="' . $image . '" alt="' . $name . '" class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: cover;">
        </div>
        <div class="flex-grow-1">
          <p class="mb-1 fw-semibold">' . $name . '</p>
          <p id="price-' . $id . '" class="mb-0 text-primary fw-bold">₦' . number_format($price) . '</p>
        </div>
        <div class="d-flex align-items-center">
          <button type="button" onclick="decreaseQuantity(' . $id . ')" class="btn btn-outline-secondary btn-sm me-2">-</button>
          <input type="number" id="quantity-' . $id . '" value="' . $qty . '" min="1" class="form-control form-control-sm w-25 text-center me-2" readonly>
          <button type="button" onclick="increaseQuantity(' . $id . ')" class="btn btn-outline-secondary btn-sm">+</button>
        </div>
        <div class="ms-3">
          <form method="post">
            <input type="hidden" name="product_id" value="' . $id . '">
            <button type="submit" name="remove_from_cart" class="btn btn-link text-danger p-0">
              <i class="bi bi-x-lg"></i>
            </button>
          </form>
        </div>
      </div>';
      $subtotal += $price * $qty;
    }

    $total = $subtotal;

    echo '
    <div class="mt-4 p-3 border rounded" style="max-width: 400px; margin: 0 auto;">
      <h4 class="text-center mb-3">CART TOTALS</h4>
      <div class="d-flex justify-content-between">
        <span>SUBTOTAL</span>
        <span id="subtotalAmount">₦' . number_format($subtotal, 2) . '</span>
      </div>
      <div class="d-flex justify-content-between mt-2">
        <span>TOTAL</span>
        <span id="totalAmount">₦' . number_format($total, 2) . '</span>
      </div>
      <div class="text-center mt-3">';
    if (!$hasError) {
        echo '<a href="order.php" class="btn btn-danger w-100">PROCEED TO CHECKOUT</a>';
    } else {
        echo '<button class="btn btn-secondary w-100" disabled>CHECKOUT UNAVAILABLE</button>';
    }
    echo '</div>
    </div>';
  } else {
    echo '<p class="text-center mt-5">Your cart is empty.</p>';
  }
  ?>
</div>

<script>
function updateQuantity(productId, change) {
    const input = document.getElementById(`quantity-${productId}`);
    let currentQty = parseInt(input.value) || 1;
    const newQty = currentQty + change;
    if (newQty < 1) return;

    input.value = newQty;

    fetch('update_quantity.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}&quantity=${newQty}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            recalculateCartTotal();
        } else {
            alert("Failed to update cart quantity.");
        }
    });
}

function increaseQuantity(productId) {
    updateQuantity(productId, 1);
}

function decreaseQuantity(productId) {
    updateQuantity(productId, -1);
}

function recalculateCartTotal() {
    let total = 0;
    document.querySelectorAll("[id^='quantity-']").forEach(input => {
        const id = input.id.replace('quantity-', '');
        const price = parseFloat(document.getElementById(`price-${id}`).textContent.replace(/₦|,/g, '')) || 0;
        const qty = parseInt(input.value) || 1;
        total += price * qty;
    });
    document.getElementById("totalAmount").textContent = "₦" + total.toLocaleString(undefined, { minimumFractionDigits: 2 });
    document.getElementById("subtotalAmount").textContent = "₦" + total.toLocaleString(undefined, { minimumFractionDigits: 2 });
}
</script>

</body>
</html>
