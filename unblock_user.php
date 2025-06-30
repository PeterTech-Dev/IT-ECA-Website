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

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    setFlashMessage('error', 'Invalid request.');
    header("Location: admin_users.php");
    exit();
}

$user_id = intval($_GET['id']);
try {
    $stmt = $conn->prepare("UPDATE users SET is_blocked = 0 WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        setFlashMessage('success', 'User unblocked successfully.');
    } else {
        throw new Exception($stmt->error);
    }
    $stmt->close();
} catch (Exception $e) {
    setFlashMessage('error', 'Error unblocking user: ' . htmlspecialchars($e->getMessage()));
    error_log("Unblock user error: " . $e->getMessage(), 3, ERROR_LOG_PATH);
}
header("Location: admin_users.php");
exit();
?>