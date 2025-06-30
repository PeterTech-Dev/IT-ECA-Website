<?php
$host     = "sql111.infinityfree.com";
$user     = "if0_39224456";
$password = "Papasmur12";
$db       = "if0_39224456_Mzansi_Market";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
