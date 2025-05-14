<?php require_once("partials/head.php") ?>
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

<body>
<?php require_once("partials/nav.php") ?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Welcome to Ahlan Ventures Tile Sales Shop</h1>
        <p>Find the perfect tiles for your home or office.</p>
        <a href="catalogue" class="btn btn-primary btn-lg">Explore Catalogue</a>
    </div>
</section>

<!-- Featured Products Section -->
<section class="featured-products py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="display-5 fw-bold text-uppercase mb-3">Sample Tiles</h2>
            <div class="divider mx-auto bg-primary" style="height: 3px; width: 80px;"></div>
        </div>

        <div class="row g-4">
            <?php
            require_once "Database.php";
            $config = require("config.php");
            $db = new Database($config['database']);
            $products = $db->query("SELECT * FROM products ORDER BY RAND() LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);

            foreach ($products as $product): ?>
                <div class="col-6 col-md-5 col-lg-3 col-sm-6">
                    <div class="card product-card h-100 border-0 shadow-sm overflow-hidden transition-all hover-shadow">
                        <div class="position-relative overflow-hidden">
                            <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top object-fit-cover"
                                 alt="<?= htmlspecialchars($product['name']) ?>" style="height: 150px;">
                        </div>
                        <div class="card-body text-center">
                            <h6 class="card-title fw-semibold"><?= htmlspecialchars($product['name']) ?></h6>
                            <p class="card-text text-primary fw-bold fs-6">₦<?= htmlspecialchars($product['price']) ?>/sq.mtr</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 text-center">
                            <?php if ($product['stock'] > 0): ?>
                                <span class="badge bg-success mb-2">In Stock (<?= $product['stock'] ?>)</span>
                                <button type="button" class="btn btn-outline-primary w-100 rounded-pill add-to-cart-btn"
                                    data-id="<?= htmlspecialchars($product['id']) ?>"
                                    data-name="<?= htmlspecialchars($product['name']) ?>"
                                    data-price="<?= htmlspecialchars($product['price']) ?>"
                                    data-image="<?= htmlspecialchars($product['image']) ?>">
                                    Add to Cart
                                </button>
                            <?php else: ?>
                                <span class="badge bg-danger mb-2">Out of Stock</span>
                                <button type="button" class="btn btn-secondary w-100 rounded-pill" disabled>
                                    Not Available
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Cart Display -->
<div class="container my-5">
    <h3>Shopping Cart</h3>
    <ul id="cart-items" class="list-group mb-3"></ul>
    <form id="cart-form" method="post" action="save_cart.php">
        <input type="hidden" name="cart_data" id="cart-data">
        <button type="submit" class="btn btn-success">Proceed</button>
    </form>
</div>

<?php require("partials/footer.php") ?>
</body>                                                                                 

<script>
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    document.addEventListener('DOMContentLoaded', () => {
        updateCartDisplay();
    });

    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const price = btn.dataset.price;
            const image = btn.dataset.image;

            const exists = cart.find(item => item.id === id);
            if (!exists) {
                cart.push({ id, name, price, image });
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartDisplay();
            } else {
                alert("Item already in cart");
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
</script>
</html>
