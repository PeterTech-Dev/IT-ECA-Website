<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to create a listing.");
}

$title = trim($_POST['title']);
$description = trim($_POST['description']);
$price = trim($_POST['price']);
$location = trim($_POST['location']);
$user_id = $_SESSION['user_id'];

// Insert into database
$stmt = $conn->prepare("INSERT INTO listings (user_id, title, description, price, location) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $title, $description, $price, $location);

if ($stmt->execute()) {
    header("Location: listings.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
