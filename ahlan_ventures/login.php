<?php  
session_start();

// Redirect to dashboard if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
require "functions.php";
require_once 'Database.php';
$config = require("config.php");
$db = new Database($config['database']);

$error = '';
$message = '';

// Login handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}

// Forgot password handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_password'])) {
    $email = $_POST['email'] ?? '';
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600);

        $stmt = $db->prepare("UPDATE users SET reset_token = :token, token_expires_at = :expires WHERE email = :email");
        $stmt->execute(['token' => $token, 'expires' => $expires, 'email' => $email]);

        $resetLink = "http://yourdomain.com/reset_password.php?token=$token";
        mail($email, "Password Reset", "Click here to reset your password: $resetLink");

        $message = "A password reset link has been sent to your email.";
    } else {
        $message = "If the email exists, a password reset link will be sent.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login Page</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #001f2b, #00f0ff20);
      height: 100vh;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-box {
      display: flex;
      max-width: 800px;
      width: 100%;
      background: #000d1a;
      box-shadow: 0 0 20px hsl(184, 100%, 50%);
      border-radius: 10px;
      overflow: hidden;
    }
    .login-form {
      flex: 1;
      padding: 40px;
      color: #fff;
    }
    .login-form h2 {
      margin-bottom: 30px;
      font-weight: bold;
    }
    .login-form input {
      background: transparent;
      border: none;
      border-bottom: 1px solid #00f0ff;
      border-radius: 0;
      color: #fff;
    }
    .login-form input::placeholder {
      color: #ccc;
    }
    .login-form .form-control:focus {
      border-color: #00f0ff;
      box-shadow: none;
    }
    .login-btn {
      background: linear-gradient(to right, #00f0ff, #00bcd4);
      border: none;
      width: 100%;
      padding: 10px;
      font-weight: bold;
      box-shadow: 0 0 10px #00f0ff;
      color: #000;
      margin-top: 20px;
    }
    .signup-link {
      text-align: center;
      margin-top: 15px;
    }
    .signup-link a {
      color: #00f0ff;
      text-decoration: none;
      cursor: pointer;
    }
    .welcome-box {
      background: rgba(111, 146, 168, 0.49);
      color: #fff;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      padding: 40px;
      text-align: center;
    }
    .welcome-box h3 {
      font-weight: bold;
    }
    .welcome-box p {
      color: #ccc;
    }
  </style>
</head>
<body>
<div class="login-box">
  <!-- Left: Form -->
  <div class="login-form">
    <h2>Login</h2>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($message): ?>
      <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST">
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <button type="submit" name="login" class="btn login-btn">Login</button>
      <div class="signup-link mt-3">
        <a onclick="document.getElementById('forgot-password-form').classList.toggle('d-none');">Forgot Password?</a>
      </div>
    </form>

    <!-- Forgot Password Form -->
    <form method="POST" class="mt-3 d-none" id="forgot-password-form">
      <input type="hidden" name="forgot_password" value="1">
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
      </div>
      <button class="btn btn-warning w-100">Send Reset Link</button>
    </form>

    <div class="signup-link">
      Don’t have an account? <a href="register.php">REGISTER</a>
    </div>
  </div>

  <!-- Right: Welcome Message -->
  <div class="welcome-box">
    <div class="text-center mb-4">
      <img src="pic/ahlanlogo.png" alt="Logo" style="max-width: 150px;">
    </div>
    <h3>WELCOME BACK!</h3>
    <p>We’re happy to have you with us back again! If you need anything, we're here to help.</p>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
