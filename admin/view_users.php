<?php
session_start();

require_once('auth.php');
require_once('../config/database.php');
checkAdminAuth();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Fetch all users with ascending order by ID
$stmt = $db->query("SELECT id, user_name, email, phone, role, created_at FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Users - ParkEase Nepal</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-dashboard {
            padding: 20px;
            max-width: 1200px;
            margin: 80px auto 0;
        }

        .user-table-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .user-table th,
        .user-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .user-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
        }

        .user-table tr:hover {
            background-color: #f9f9f9;
        }

        .role-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .role-admin {
            background-color: #ffd700;
            color: #333;
        }

        .role-user {
            background-color: #e0e0e0;
            color: #333;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ffd700;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: #e6c200;
        }
    </style>
</head>
<body>
    <!-- Reuse the same admin navigation from dashboard.php -->
    <nav class="admin-nav">
        <div class="nav-container">
            <div class="logo">
                <h1>ParkEase Nepal - Admin</h1>
            </div>
            <div class="nav-links">
                <a href="../index.php"><i class="fas fa-home"></i> Main Site</a>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="logout.php" class="admin-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="admin-dashboard">
        <a href="dashboard.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
        
        <div class="user-table-container">
            <h2>User Management</h2>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo strtolower($user['role']); ?>">
                                <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>