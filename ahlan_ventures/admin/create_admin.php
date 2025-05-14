<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php

require_once "../Database.php";     // Goes up one folder to find Database.php
$config = require_once "../config.php";



// Create a new database instance
$db = new Database($config['database']);

// Create password hash
$passwordHash = password_hash('admin123', PASSWORD_DEFAULT);

// Insert new admin user
$stmt = $db->prepare("INSERT INTO admins (username, password) VALUES (:u, :p)");
$stmt->execute([
    'u' => 'admin',
    'p' => $passwordHash
]);

echo "✅ Admin user created successfully.";
