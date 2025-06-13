<?php
session_start();
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['items'])) {
    echo json_encode(["success" => false, "error" => "Invalid data"]);
    exit();
}

$cart = ['items' => [], 'total' => 0];

foreach ($data['items'] as $item) {
    $title = $item['title'];
    $qty = $item['qty'];

    // Fetch price and weight from DB
    $stmt = $conn->prepare("SELECT price, weight FROM listings WHERE title = ?");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        $price = (float)$result['price'];
        $weight = (float)$result['weight'];

        $cart['items'][] = [
            'title' => $title,
            'price' => $price,
            'qty' => $qty,
            'weight' => $weight
        ];

        $cart['total'] += $price * $qty;
    }
}

$_SESSION["cart"] = $cart;

echo json_encode(["success" => true]);
