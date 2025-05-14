
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require "functions.php";?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php require("views/partials/nav.php"); ?>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow p-4">
        <h2 class="mb-4">Welcome, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Guest') ?>

        
        <div class="row g-3">
            <div class="col-md-4">
                <a href="catalogue" class="btn btn-primary w-100">Browse Tiles</a>
            </div>
            <div class="col-md-4">
                <a href="cart.php" class="btn btn-outline-success w-100">View Cart</a>
            </div>
            <div class="col-md-4">
                <a href="order.php" class="btn btn-outline-dark w-100">My Orders</a>
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

</body>
</html>

