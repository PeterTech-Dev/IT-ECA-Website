<?php
session_start();
require_once 'config.php';
require_once 'flash.php';
include 'db.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    setFlashMessage('error', 'Access denied.');
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    setFlashMessage('error', 'Invalid request.');
    header("Location: admin_users.php");
    exit();
}

$user_id = intval($_POST['id']);
try {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        setFlashMessage('success', 'User deleted successfully.');
    } else {
        throw new Exception($stmt->error);
    }
    $stmt->close();
} catch (Exception $e) {
    setFlashMessage('error', 'Error deleting user: ' . htmlspecialchars($e->getMessage()));
    error_log("Delete user error: " . $e->getMessage(), 3, ERROR_LOG_PATH);
}
header("Location: admin_users.php");
exit();
?>