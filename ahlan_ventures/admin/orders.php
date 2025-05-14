<?php
session_start();


require_once "../Database.php";
$config = require("../config.php");
$db = new Database($config['database']);



// Query order details
$orders = $db->query("
  SELECT orders.id, users.first_name, products.name AS product_name, orders.quantity, orders.total_price, orders.created_at
  FROM orders
  JOIN users ON orders.user_id = users.id
  JOIN products ON orders.product_id = products.id
  ORDER BY orders.created_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>orders</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap & Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f6fa;
    }
    .sidebar {
      height: 100vh;
      background: #343a40;
      color: #fff;
      padding-top: 1rem;
      position: fixed;
      width: 220px;
    }
    .sidebar a {
      color: #c2c7d0;
      text-decoration: none;
      display: block;
      padding: 0.75rem 1rem;
    }
    .sidebar a:hover, .sidebar a.active {
      background: #495057;
      color: #fff;
    }
    .main-content {
      margin-left: 220px;
      padding: 2rem;
    }
    .topbar {
      background: #fff;
      padding: 1rem 2rem;
      border-bottom: 1px solid #dee2e6;
    }
    .card {
      border: none;
      border-radius: 0.75rem;
      box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }
    .table th, .table td {
      vertical-align: middle;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<?php require("views/sidebar.php"); ?>

<!-- Main Content -->
<div class="main-content">
  <div class="topbar d-flex justify-content-between align-items-center">
    <h4>ORDERS </h4>
    <span class="text-muted"><?= date("l, F jS Y") ?></span>
  </div>

  <!-- Orders Table -->
  <div class="mt-4">
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Orders</h5>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped table-hover">
          <thead class="table-light">
            <tr>
              <th>Order ID</th>
              <th>User</th>
              <th>Product</th>
              <th>Qty</th>
              <th>Total</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td><?= $order['id'] ?></td>
                <td><?= htmlspecialchars($order['first_name']) ?></td>
                <td><?= htmlspecialchars($order['product_name']) ?></td>
                <td><?= $order['quantity'] ?></td>
                <td>₦<?= number_format($order['total_price'], 2) ?></td>
                <td><?= date("M j, Y H:i", strtotime($order['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
              <tr><td colspan="6" class="text-center">No orders found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</body>
</html>
