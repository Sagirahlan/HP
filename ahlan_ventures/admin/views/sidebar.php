
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
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
<nav class="sidebar">
  <div class="text-center mb-4">
    <img src="../pic/ahlanlogo.png" alt="Ahlan Logo" class="img-fluid mb-2" style="max-width: 100px;">
    <h4>Admin</h4>
  </div>
  <?php
  $current_page = basename($_SERVER['PHP_SELF']);
  ?>

  <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
  <a href="products.php" class="<?= $current_page == 'products.php' ? 'active' : '' ?>"><i class="fas fa-box me-2"></i> Products</a>
  <a href="users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>"><i class="fas fa-users me-2"></i> Users</a>
  <a href="orders.php" class="<?= $current_page == 'orders.php' ? 'active' : '' ?>"><i class="fas fa-file-invoice me-2"></i> Orders</a>
  <a href="../register.php" class="<?= $current_page == 'register.php' ? 'active' : '' ?>"><i class="fas fa-user-plus me-2"></i> Register User</a>
  <a href="../login.php" class="<?= $current_page == 'login.php' ? 'active' : '' ?>"><i class="fas fa-sign-in-alt me-2"></i> Login User</a>
  <a href="logout.php" class="<?= $current_page == 'logout.php' ? 'active' : '' ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</nav>