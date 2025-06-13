<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

if (isset($_GET['id'])) {
    $listing_id = intval($_GET['id']);

    // Check if this listing belongs to the logged-in user
    $stmt = $conn->prepare("SELECT user_id FROM listings WHERE id = ?");
    $stmt->bind_param("i", $listing_id);
    $stmt->execute();
    $stmt->bind_result($listing_user_id);
    $stmt->fetch();
    $stmt->close();

    if ($listing_user_id !== $_SESSION['user_id']) {
        die("Unauthorized to delete this listing.");
    }

    // Delete the listing
    $stmt = $conn->prepare("DELETE FROM listings WHERE id = ?");
    $stmt->bind_param("i", $listing_id);
    $stmt->execute();
    $stmt->close();

    header("Location: listings.php");
    exit();
} else {
    echo "No listing ID provided.";
}
?>
