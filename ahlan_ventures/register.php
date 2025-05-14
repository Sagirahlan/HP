<?php 
session_start();


// Redirect to dashboard if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
require "functions.php";

require "Database.php";
$config = require("config.php");


$db = new Database($config['database']);
$posts = $db->query("select * from users")->fetchAll(PDO::FETCH_ASSOC);?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>register Page</title>
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

    .register-box {
      display: flex;
      max-width: 800px;
      width: 100%;
      background: #000d1a;
      box-shadow: 0 0 20px #00f0ff;
      border-radius: 10px;
      overflow: hidden;
    }

    .register-form {
      flex: 1;
      padding: 40px;
      color: #fff;
    }

    .register-form h2 {
      margin-bottom: 30px;
      font-weight: bold;
    }

    .register-form input {
      background: transparent;
      border: none;
      border-bottom: 1px solid #00f0ff;
      border-radius: 0;
      color: #fff;
    }

    .register-form input::placeholder {
      color: #ccc;
    }

    .register-form .form-control:focus {
      border-color: #00f0ff;
      box-shadow: none;
    }

    .register-btn {
      background: linear-gradient(to right, #00f0ff, #00bcd4);
      border: none;
      width: 100%;
      padding: 10px;
      font-weight: bold;
      box-shadow: 0 0 10px #00f0ff;
      color: #000;
      margin-top: 20px;
    }

    .login-link {
      text-align: center;
      margin-top: 15px;
    }

    .login-link a {
      color: #00f0ff;
      text-decoration: none;
    }

    .welcome-box {
      background:rgba(111, 146, 168, 0.49);
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

<div class="register-box" style="height: 90%; max-height: 600px;">
  <!-- Left: Form -->
  <div class="register-form" style="overflow-y: auto;">
    <h2>REGISTER</h2>
    <form method="POST">
      <div class="mb-3">
        <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
      </div>
      <div class="mb-3">
        <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
      </div>
      <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
      </div>
      <div class="mb-3">
        <input type="text" name="phone_number" class="form-control" placeholder="Phone Number" required>
      </div>
      <div class="mb-3">
        <input type="text" name="street_name" class="form-control" placeholder="Street Name" required>
      </div>
      <div class="mb-3">
        <input type="text" name="region" class="form-control" placeholder="Region" required>
      </div>
      <div class="mb-3">
        <input type="text" name="address" class="form-control" placeholder="Address" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <div class="mb-3">
        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
      </div>
      <button type="submit" name="register" class="btn register-btn">Register</button>
    </form>
    <div class="login-link">
      Already have an account? <a href="login.php">Login</a>
    </div>
  </div>

  <!-- Right: Welcome Message -->
  <div class="welcome-box" style="overflow-y: auto;">
     <!-- Logo -->
     <div class="text-center mb-4">
      <img src="pic/ahlanlogo.png" alt="Logo" style="max-width: 150px;">
    </div>
    <h3>WELCOME!</h3>
    <p>We’re happy to have you here. If you need anything, we're here to help.</p>
  </div>
</div>

</body> 
<?php
if (isset($_POST["register"])) {
    $firstName = $_POST["first_name"];
    $lastName = $_POST["last_name"];
    $email = $_POST["email"];
    $phone = $_POST["phone_number"];
    $street = $_POST["street_name"];
    $region = $_POST["region"];
    $address = $_POST["address"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($password !== $confirmPassword) {
        echo "Error: Passwords do not match!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (first_name, last_name, email, phone_number, street_name, region, address, password) 
                VALUES (:first_name, :last_name, :email, :phone_number, :street_name, :region, :address, :password)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_number', $phone);
        $stmt->bindParam(':street_name', $street);
        $stmt->bindParam(':region', $region);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':password', $hashedPassword);

        //mark that user is logged in.
        $_SESSION['user'] = [
          'email' => $email
        ];
         
        //if email exists, show error message

        $checkEmailQuery = "SELECT COUNT(*) FROM users WHERE email = :email";
        $checkStmt = $db->prepare($checkEmailQuery);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();
        $emailExists = $checkStmt->fetchColumn();

        if ($emailExists) {
            echo "Error: Email already exists!";
        } else {
            if ($stmt->execute()) {
                echo "Registration successful!";
            } else {
                echo "Error: " . implode(", ", $stmt->errorInfo());
            }
        }
    }
}
?>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>