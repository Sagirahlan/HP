<?php 
session_start();
// Start output buffering to prevent accidental output before redirect
ob_start();

require "functions.php";
require_once "Database.php";
$config = require("config.php");

$db = new Database($config['database']);


$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'] ?? null;
    $cartItems = $_SESSION['cart'] ?? [];

    if (!$userId) {
        header("Location: login.php");
        exit;
    }

    if (empty($cartItems)) {
        $errorMsg = "Cart is empty.";
    } else {
        // Get user info
        $stmt = $db->prepare("SELECT first_name, email, phone_number FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if (!$user) {
            $errorMsg = "User information not found.";
        } else {
            // Calculate total
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $totalAmount += $item['price'] * ($item['quantity'] ?? 1);
            }

            // NABRoll API credentials
            $publicApiKey = 'Pk_TeStHV9FnLZE1vSidgkH36b4s473lpKYkI58gYgc6М';
            $secretApiKey = 'Sk_teSTN-HY[n1]wIO32A-AU0XP5kRZ[tZHpOxQбbf9]]';
            $apiEndpoint = 'https://demo.nabroll.com.ng/api/v1/transactions/initiate';

            $payerRefNo = 'ORD' . time() . rand(100, 999);
            $hashString = $payerRefNo . $totalAmount . $publicApiKey;
            $hash = hash_hmac('sha256', $hashString, $secretApiKey);

            $postData = [
                'ApiKey' => $publicApiKey,
                'Hash' => $hash,
                'Amount' => $totalAmount,
                'PayerRefNo' => $payerRefNo,
                'PayerName' => $user['first_name'],
                'Email' => $user['email'],
                'phone_number' => $user['phone_number'],
                'Description' => 'Tile Order Payment',
                'ResponseUrl' => 'https://yourdomain.com/nabrollResponse.php',
                'MetaData' => 'UserID: ' . $userId,
                'FeeBearer' => 'Customer'
            ];

            // Send cURL request
            $ch = curl_init($apiEndpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $curlError = curl_error($ch);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($curlError) {
                $errorMsg = "cURL error: " . $curlError;
            } elseif (!$result || !isset($result['status'])) {
                $errorMsg = "Invalid response from payment gateway.";
            } elseif ($result['status'] === 'SUCCESSFUL' && !empty($result['PaymentUrl'])) {
                $_SESSION['pending_payment'] = [
                    'payerRefNo' => $payerRefNo,
                    'amount' => $totalAmount,
                    'cart' => $cartItems
                ];
                header("Location: " . $result['PaymentUrl']);
                exit;
            } else {
                $errorMsg = $result['msg'] ?? 'Payment initiation failed.';
            }
        }
    }
}

// End output buffering
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Place Order</title>
  <?php require("views/partials/head.php"); ?>
</head>
<body>
  <?php require("views/partials/nav.php"); ?>

  <div class="container mt-5">
    <h2 class="text-center mb-4">Confirm Your Order</h2>

    <?php if (!empty($errorMsg)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <form method="POST" class="row">
      <div class="col-md-6 offset-md-3">
        <h4>Order Summary</h4>
        <ul class="list-group mb-3">
          <?php
          $cartItems = $_SESSION['cart'] ?? ($_SESSION['pending_payment']['cart'] ?? []);
          $subtotal = 0;

          if (!empty($cartItems)):
              foreach ($cartItems as $item):
                  $itemTotal = $item['price'] * ($item['quantity'] ?? 1);
                  $subtotal += $itemTotal;
          ?>
            <li class="list-group-item d-flex justify-content-between">
              <span><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?? 1 ?></span>
              <strong>₦<?= number_format($itemTotal, 2) ?></strong>
            </li>
          <?php
              endforeach;
          ?>
            <li class="list-group-item d-flex justify-content-between">
              <strong>Total</strong>
              <strong>₦<?= number_format($subtotal, 2) ?></strong>
            </li>
          <?php else: ?>
            <li class="list-group-item text-danger">No items in cart.</li>
          <?php endif; ?>
        </ul>
        <button type="submit" class="btn btn-danger w-100">Place Order</button>
      </div>
    </form>
  </div>
</body>
</html>
