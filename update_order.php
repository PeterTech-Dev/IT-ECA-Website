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

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    setFlashMessage('error', 'Invalid request.');
    header("Location: admin_orders.php");
    exit();
}

$order_id = intval($_GET['id']);
$status = $_GET['status'];
if (!in_array($status, ['SHIPPED', 'CANCELLED'])) {
    setFlashMessage('error', 'Invalid status.');
    header("Location: admin_orders.php");
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    if ($stmt->execute()) {
        setFlashMessage('success', "Order $status successfully.");
    } else {
        throw new Exception($stmt->error);
    }
    $stmt->close();
} catch (Exception $e) {
    setFlashMessage('error', 'Error updating order: ' . htmlspecialchars($e->getMessage()));
    error_log("Update order error: " . $e->getMessage(), 3, ERROR_LOG_PATH);
}
header("Location: admin_orders.php");
exit();
?>