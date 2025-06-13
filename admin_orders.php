<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Orders - Mzansi Market</title>
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body>
  <div class="admin-layout">
    <header class="sticky-header">
      <div class="container header-container">
        <h1>Manage Orders</h1>
        <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
      </div>
    </header>

    <div class="container admin-content">
      <aside class="admin-sidebar">
        <nav class="admin-nav">
          <a href="admin_users.php" class="nav-link">Manage Users</a>
          <a href="admin_listings.php" class="nav-link">Manage Listings</a>
          <a href="admin_orders.php" class="nav-link active">Manage Orders</a>
        </nav>
      </aside>

      <main class="admin-main">
        <section class="table-section">
          <div class="table-header">
            <h2>Order List</h2>
            <input type="text" id="order-search" placeholder="Search orders..." class="search-bar">
          </div>
          <table id="orders-table">
            <thead>
              <tr>
                <th data-sort="id">ID <span class="sort-icon">↕</span></th>
                <th data-sort="user_id">User ID <span class="sort-icon">↕</span></th>
                <th data-sort="total">Amount <span class="sort-icon">↕</span></th>
                <th data-sort="created_at">Date <span class="sort-icon">↕</span></th>
                <th data-sort="status">Status <span class="sort-icon">↕</span></th>
              </tr>
            </thead>
            <tbody>
              <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $order['id'] ?></td>
                  <td><?= $order['user_id'] ?></td>
                  <td>R<?= number_format($order['total'], 2) ?></td>
                  <td><?= $order['created_at'] ?></td>
                  <td><?= htmlspecialchars($order['status']) ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
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

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Search functionality
      const searchInput = document.getElementById('order-search');
      const tableRows = document.querySelectorAll('#orders-table tbody tr');

      searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        tableRows.forEach(row => {
          const id = row.cells[0].textContent.toLowerCase();
          const userId = row.cells[1].textContent.toLowerCase();
          const status = row.cells[4].textContent.toLowerCase();
          row.style.display = id.includes(searchTerm) || userId.includes(searchTerm) || status.includes(searchTerm) ? '' : 'none';
        });
      });

      // Sorting functionality
      document.querySelectorAll('#orders-table th[data-sort]').forEach(header => {
        header.addEventListener('click', () => {
          const sortKey = header.getAttribute('data-sort');
          const sortIcon = header.querySelector('.sort-icon');
          const isAsc = sortIcon.textContent === '↑' || sortIcon.textContent === '↕';
          
          tableRows.forEach(row => row.remove());
          const sortedRows = Array.from(tableRows).sort((a, b) => {
            let valA = a.cells[sortKey === 'id' ? 0 : sortKey === 'user_id' ? 1 : sortKey === 'total' ? 2 : sortKey === 'created_at' ? 3 : 4].textContent;
            let valB = b.cells[sortKey === 'id' ? 0 : sortKey === 'user_id' ? 1 : sortKey === 'total' ? 2 : sortKey === 'created_at' ? 3 : 4].textContent;
            
            if (sortKey === 'id' || sortKey === 'user_id') valA = parseInt(valA), valB = parseInt(valB);
            if (sortKey === 'total') valA = parseFloat(valA.replace('R', '')), valB = parseFloat(valB.replace('R', ''));
            return isAsc ? valA > valB ? 1 : -1 : valA < valB ? 1 : -1;
          });

          document.querySelector('#orders-table tbody').append(...sortedRows);
          document.querySelectorAll('.sort-icon').forEach(icon => icon.textContent = '↕');
          sortIcon.textContent = isAsc ? '↓' : '↑';
        });
      });
    });
  </script>
</body>
</html>