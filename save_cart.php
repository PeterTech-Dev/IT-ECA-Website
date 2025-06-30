<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['items']) || !is_array($data['items'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid cart format.']);
        exit;
    }

    // Clear existing cart
    $deleteStmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    if (!$deleteStmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to prepare DELETE statement']);
        exit;
    }
    $deleteStmt->bind_param("i", $user_id);
    $deleteStmt->execute();

    // Prepare insert
    $stmt = $conn->prepare("INSERT INTO cart (user_id, listing_id, quantity, added_at, weight_snapshot) VALUES (?, ?, ?, NOW(), ?)");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to prepare INSERT statement']);
        exit;
    }

    foreach ($data['items'] as $item) {
        $listing_id = intval($item['id']);
        $quantity = intval($item['quantity']);
        $weight = floatval($item['weight']);

        $stmt->bind_param("iiid", $user_id, $listing_id, $quantity, $weight);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
