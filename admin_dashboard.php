<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard - Mzansi Market</title>
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body>
  <div class="admin-layout">
    <header class="sticky-header">
      <div class="container header-container">
        <h1>Admin Dashboard</h1>
        <a href="index.php" class="back-link">← Return to Home</a>
      </div>
    </header>

    <div class="container admin-content">
      <aside class="admin-sidebar">
        <nav class="admin-nav">
          <a href="admin_users.php" class="nav-link">Manage Users</a>
          <a href="admin_listings.php" class="nav-link">Manage Listings</a>
          <a href="admin_orders.php" class="nav-link">Manage Orders</a>
        </nav>
      </aside>

      <main class="admin-main">
        <section class="dashboard-section">
          <h2>Welcome, Admin</h2>
          <p>Use the sidebar to manage users, listings, and orders.</p>
        </section>
      </main>
    </div>
  </div>

  <div class="modal" id="confirmation-modal">
    <div class="modal-content">
      <p id="modal-message"></p>
      <div class="modal-actions">
        <button class="modal-button confirm">Confirm</button>
        <button class="modal-button cancel">Cancel</button>
      </div>
    </div>
  </div>
</body>
</html>