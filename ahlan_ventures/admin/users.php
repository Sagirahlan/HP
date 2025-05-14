<?php
session_start();
require_once "../Database.php";
$config = require("../config.php");
$db = new Database($config['database']);

// Fetch all registered users
$users = $db->query("SELECT id, first_name, last_name, email, region, street_name, phone_number, address, created_at FROM users ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registered Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<?php require("views/sidebar.php"); ?>

<div class="container my-5 d-flex justify-content-end">
    <div class="card p-4" style="width: 90%;">
        <h2 class="text-center mb-4 text-primary">Registered Users</h2>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Street Name</th>
                        <th>Address</th>
                        <th>Region</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="text-center"><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone_number']) ?></td>
                            <td><?= htmlspecialchars($user['street_name']) ?></td>
                            <td><?= htmlspecialchars($user['address']) ?></td>
                            <td><?= htmlspecialchars($user['region']) ?></td>
                            <td><?= date('d M Y, h:i A', strtotime($user['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="8" class="text-center text-muted">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="8" class="text-center text-muted">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
