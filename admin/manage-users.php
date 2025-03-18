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

// Handle delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $csrf_token = filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_STRING);

    // Verify CSRF token
    if (!$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid security token";
    } elseif (!$userId) {
        $_SESSION['error'] = "Invalid user ID";
    } else {
        try {
            // Check if trying to delete own account
            if ($userId == $_SESSION['user_id']) {
                throw new Exception("You cannot delete your own admin account");
            }

            // Begin transaction
            $db->beginTransaction();

            // Delete related records first (bookings, etc.)
            $stmt = $db->prepare("DELETE FROM bookings WHERE user_id = ?");
            $stmt->execute([$userId]);

            // Delete user
            $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
            if ($stmt->execute([$userId])) {
                if ($stmt->rowCount() > 0) {
                    $db->commit();
                    $_SESSION['success'] = "User deleted successfully";
                } else {
                    throw new Exception("Cannot delete admin users");
                }
            } else {
                throw new Exception("Failed to delete user");
            }
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = $e->getMessage();
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Generate new CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

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
        /* Previous styles remain the same */


        .delete-btn {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .delete-btn:hover {
            background-color: #cc0000;
        }

        .delete-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
        }

        .modal-actions {
            margin-top: 20px;
            text-align: right;
        }

        .modal-actions button {
            margin-left: 10px;
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }

        .confirm-delete {
            background-color: #ff4444;
            color: white;
        }

        .cancel-delete {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation remains the same -->
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
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo htmlspecialchars($_SESSION['success']); 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

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
                        <th>Actions</th>
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
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id'] && $user['role'] != 'admin'): ?>
                                <button 
                                    class="delete-btn"
                                    onclick="showDeleteConfirmation(<?php echo htmlspecialchars($user['id']); ?>, '<?php echo htmlspecialchars($user['user_name']); ?>')"
                                >
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to delete user <strong id="deleteUserName"></strong>?</p>
            <p>This action cannot be undone.</p>
            <div class="modal-actions">
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="user_id" id="deleteUserId">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="delete_user" value="1">
                    <button type="button" class="cancel-delete" onclick="hideDeleteModal()">Cancel</button>
                    <button type="submit" class="confirm-delete">Delete User</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showDeleteConfirmation(userId, userName) {
            document.getElementById('deleteModal').style.display = 'block';
            document.getElementById('deleteUserId').value = userId;
            document.getElementById('deleteUserName').textContent = userName;
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                hideDeleteModal();
            }
        }
    </script>
</body>
</html>