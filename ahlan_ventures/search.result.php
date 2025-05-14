<?php 
require "functions.php";
require_once "Database.php";

$config = require("config.php");
$db = new Database($config['database']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Result</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .product-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .hover-shadow {
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .transition-all {
      transition: all 0.3s ease;
    }
    .hover-opacity-100:hover {
      opacity: 1 !important;
    }
    .object-fit-cover {
      object-fit: cover;
    }
  </style>
</head>
<body>
<?php require("views/partials/head.php"); ?>
<?php require("views/partials/nav.php"); ?>

<section class="featured-products py-5 bg-light">
  <div class="container">
    <div class="section-header text-center mb-5">
      <h2 class="display-5 fw-bold text-uppercase mb-3">SEARCHED PRODUCT</h2>
      <div class="divider mx-auto bg-primary" style="height: 3px; width: 50px;"></div>
    </div>

    <div class="row g-4">
      <?php
      if (isset($_GET['id'])) {
          $productId = intval($_GET['id']);
          $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
          $stmt->execute(['id' => $productId]);
          $product = $stmt->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="col-6 col-md-6 col-lg-4 col-xl-3">
      <div class="card product-card h-100 border-0 shadow-sm overflow-hidden transition-all hover-shadow">
         <div class="position-relative overflow-hidden">
            <img src="<?= htmlspecialchars($product['image'] ?? '') ?>" class="card-img-top object-fit-cover" alt="<?= htmlspecialchars($product['name'] ?? '') ?>" style="height: 220px;">
            <div class="card-img-overlay d-flex align-items-end justify-content-center bg-dark bg-opacity-25 opacity-0 hover-opacity-100 transition-all">
               <button class="btn btn-primary rounded-pill px-4 quick-view-btn"
                           data-bs-toggle="modal"
                           data-bs-target="#productModal"
                           data-product-id="<?= htmlspecialchars($product['id']) ?>"
                           data-product-name="<?= htmlspecialchars($product['name']) ?>"
                           data-product-price="<?= htmlspecialchars($product['price']) ?>"
                           data-product-image="<?= htmlspecialchars($product['image']) ?>"
                           data-product-description="<?= htmlspecialchars($product['description']) ?>"
                           data-stock="<?= htmlspecialchars($product['stock']) ?>">
                  Quick View
               </button>
            </div>
         </div>
         <div class="card-body text-center">
            <h5 class="card-title fw-semibold"><?= htmlspecialchars($product['name']) ?></h5>
            <p class="card-text text-primary fw-bold fs-5">₦<?= htmlspecialchars($product['price']) ?>/sq.mtr</p>
         </div>
         <div class="card-footer bg-transparent border-0 pt-0">
            <?php if ($product['stock'] > 0): ?>
                  <div class="d-flex justify-content-center">
                    <span class="badge bg-success mb-2">In Stock (<?= $product['stock'] ?>)</span>
                  </div>
               <button type="button" class="btn btn-outline-primary w-100 rounded-pill add-to-cart-btn"
                           data-id="<?= htmlspecialchars($product['id']) ?>"
                           data-name="<?= htmlspecialchars($product['name']) ?>"
                           data-price="<?= htmlspecialchars($product['price']) ?>"
                           data-image="<?= htmlspecialchars($product['image']) ?>">
                  Add to Cart
               </button>
            <?php else: ?>
                  <div class="d-flex justify-content-center">
                    <span class="badge bg-danger mb-2">Out of Stock</span>
                  </div>
               <button type="button" class="btn btn-secondary w-100 rounded-pill" disabled>
                  Not Available
               </button>
            <?php endif; ?>
         </div>
      </div>
      </div>
      <?php } ?>
    </div>
  </div>
</section>

<div class="container my-5">
  <h3>Shopping Cart</h3>
  <ul id="cart-items" class="list-group mb-3"></ul>
  <form id="cart-form" method="post" action="save_cart.php">
    <input type="hidden" name="cart_data" id="cart-data">
    <button type="submit" class="btn btn-success">Proceed</button>
  </form>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="productModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <img id="productModalImage" src="" alt="" class="img-fluid mb-3">
        <p id="productModalDescription"></p>
        <p><strong>Price:</strong> ₦<span id="productModalPrice"></span></p>
        <p><strong>Stock:</strong> <span id="productModalStock"></span> sq.mtr</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
  let cart = JSON.parse(localStorage.getItem('cart')) || [];

  document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      const name = btn.dataset.name;
      const price = btn.dataset.price;
      const image = btn.dataset.image;
      const stock = parseInt(btn.dataset.stock);

      const exists = cart.find(item => item.id === id);
      if (!exists && stock > 0) {
        cart.push({ id, name, price, image, stock });
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartDisplay();
      } else if (exists) {
        alert("Item already in cart");
      } else {
        alert("Out of stock");
      }
    });
  });

  function updateCartDisplay() {
    const list = document.getElementById('cart-items');
    list.innerHTML = '';
    cart.forEach(item => {
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.innerHTML = `
        <span>
          <img src="${item.image}" width="50" class="me-2"> ${item.name} - ₦${item.price}
        </span>
        <button class="btn btn-sm btn-danger" onclick="removeFromCart('${item.id}')">Remove</button>
      `;
      list.appendChild(li);
    });

    document.getElementById('cart-data').value = JSON.stringify(cart);
  }

  function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
  }

  document.addEventListener('DOMContentLoaded', () => {
    updateCartDisplay();

    const productModal = document.getElementById('productModal');
    productModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const productName = button.getAttribute('data-product-name');
      const productPrice = button.getAttribute('data-product-price');
      const productImage = button.getAttribute('data-product-image');
      const productDescription = button.getAttribute('data-product-description');
      const stock = button.getAttribute('data-stock');

      productModal.querySelector('.modal-title').textContent = productName;
      productModal.querySelector('#productModalImage').src = productImage;
      productModal.querySelector('#productModalDescription').textContent = productDescription;
      productModal.querySelector('#productModalPrice').textContent = productPrice;
      productModal.querySelector('#productModalStock').textContent = stock;
    });
  });
</script>

<?php require("views/partials/footer.php"); ?>
</body>
</html>
