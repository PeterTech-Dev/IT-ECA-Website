<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Fetch the existing hashed password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Verify current password
    if (!password_verify($current_password, $hashed_password)) {
        echo "<p style='color: red; text-align:center;'>❌ Current password is incorrect.</p>";
        exit();
    }

    // Hash and update the new password
    $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->bind_param("si", $new_hashed, $user_id);

    if ($update->execute()) {
        echo "<p style='color: green; text-align:center;'>✅ Password updated successfully!</p>";
        echo "<p style='text-align:center;'><a href='profile.php'>← Back to Profile</a></p>";
    } else {
        echo "<p style='color: red; text-align:center;'>❌ Error updating password.</p>";
    }

    $update->close();
}
?>
