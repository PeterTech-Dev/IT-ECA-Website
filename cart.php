<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $productId = intval($_POST["id"]);
    
    // Optional: store in session or database
    session_start();
    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }
    $_SESSION["cart"][] = $productId;

    echo "Added to cart";
} else {
    echo "Invalid request";
}
?>
