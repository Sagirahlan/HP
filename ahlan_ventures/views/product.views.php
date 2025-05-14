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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>catalogue View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
   <?php require_once("partials/nav.php") ?>
   


<!-- Featured Products Section -->
<section class="featured-products py-5 bg-light">
     <div class="container">
          <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold text-uppercase mb-3">Featured Tiles</h2>
                <div class="divider mx-auto bg-primary" style="height: 3px; width: 80px;"></div>
          </div>
          
          <div class="row g-4">
                <!-- Dynamic Product Cards -->
                <?php
                // Fetch products from the database with pagination
                require_once "Database.php";
                $config = require("config.php");
                $db = new Database($config['database']);

                // Pagination logic
                $limit = 8; // Number of products per page
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($page - 1) * $limit;

                $totalProducts = $db->query("SELECT COUNT(*) as count FROM products")->fetch(PDO::FETCH_ASSOC)['count'];
                $totalPages = ceil($totalProducts / $limit);

                $products = $db->query("SELECT * FROM products LIMIT $limit OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);

                foreach ($products as $product): ?>
                     <div class="col-6 col-md-6 col-lg-4 col-xl-3">
                          <div class="card product-card h-100 border-0 shadow-sm overflow-hidden transition-all hover-shadow">
                                <div class="position-relative overflow-hidden">
                                   <img src="<?= htmlspecialchars($product['image'] ?? '') ?>" class="card-img-top object-fit-cover" alt="<?= htmlspecialchars($product['name'] ?? '') ?>" style="height: 220px;">
                                     <div class="card-img-overlay d-flex align-items-end justify-content-center bg-dark bg-opacity-25 opacity-0 hover-opacity-100 transition-all">
                                        <button class="btn btn-primary rounded-pill px-4 quick-view-btn" data-bs-toggle="modal" data-bs-target="#productModal" data-product-id="<?= htmlspecialchars($product['id'] ?? '') ?>" data-product-name="<?= htmlspecialchars($product['name'] ?? '') ?>" data-product-price="<?= htmlspecialchars($product['price'] ?? '') ?>" data-product-image="<?= htmlspecialchars($product['image'] ?? '') ?>" data-product-description="<?= htmlspecialchars($product['description'] ?? '') ?>">Quick View</button>
                                     </div>
                                </div>
                                <div class="card-body text-center">
                                   <h5 class="card-title fw-semibold"><?= htmlspecialchars($product['name'] ?? '') ?></h5>
                                   <p class="card-text text-primary fw-bold fs-5">₦<?= htmlspecialchars($product['price'] ?? '') ?>/sq.mtr</p>
                                </div>
                                <div class="card-footer bg-transparent border-0 pt-0">
                                     <form class="add-to-cart-form" data-product-id="<?= htmlspecialchars($product['id']) ?>" data-product-name="<?= htmlspecialchars($product['name']) ?>" data-product-price="<?= htmlspecialchars($product['price']) ?>">
                                          <button type="button" class="btn btn-outline-primary w-100 rounded-pill add-to-cart-btn">Add to Cart</button>
                                     </form>
                                </div>
                          </div>
                     </div>
                <?php endforeach; ?>
          </div>

          <!-- Pagination -->
          <div class="d-flex justify-content-center mt-5">
                <nav aria-label="Page navigation">
                     <ul class="pagination">
                          <?php if ($page > 1): ?>
                               <li class="page-item">
                                    <a class="page-link rounded-circle mx-1" href="?page=<?= $page - 1 ?>" aria-label="Previous" style="width: 40px; height: 40px; line-height: 40px; text-align: center;">
                                         <span aria-hidden="true">&laquo;</span>
                                    </a>
                               </li>
                          <?php endif; ?>

                          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                               <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link rounded-circle mx-1" href="?page=<?= $i ?>" style="width: 40px; height: 40px; line-height: 40px; text-align: center;"><?= $i ?></a>
                               </li>
                          <?php endfor; ?>

                          <?php if ($page < $totalPages): ?>
                               <li class="page-item">
                                    <a class="page-link rounded-circle mx-1" href="?page=<?= $page + 1 ?>" aria-label="Next" style="width: 40px; height: 40px; line-height: 40px; text-align: center;">
                                         <span aria-hidden="true">&raquo;</span>
                                    </a>
                               </li>
                          <?php endif; ?>
                     </ul>
                </nav>
          </div>
     </div>
</section>
 <!-- Modal -->
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
                    <p><strong>Price:</strong> N<span id="productModalPrice"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productModal = document.getElementById('productModal');
            productModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const productName = button.getAttribute('data-product-name');
                const productPrice = button.getAttribute('data-product-price');
                const productImage = button.getAttribute('data-product-image');
                const productDescription = button.getAttribute('data-product-description');

                productModal.querySelector('.modal-title').textContent = productName;
                productModal.querySelector('#productModalImage').src = productImage;
                productModal.querySelector('#productModalDescription').textContent = productDescription;
                productModal.querySelector('#productModalPrice').textContent = productPrice;
            });
        });
    </script>


   <!-- Footer -->
 
</body>

</html>
<?php require("partials/footer.php") ?>