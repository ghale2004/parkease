<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkAdminAuth() {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        header('Location: ../login.php');
        exit();
    }
}
