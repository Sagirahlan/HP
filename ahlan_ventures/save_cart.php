<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_data'])) {
    $cart = json_decode($_POST['cart_data'], true);
    $_SESSION['cart'] = $cart;

    // Optional: Redirect to checkout or confirmation page
    header("Location: cart.php");
    exit();
} else {
    echo "No cart data received.";
}
?>
