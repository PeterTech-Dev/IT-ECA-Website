<?php
session_start();
include 'db.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Fetch users from DB
$sql = "SELECT id, username, email, is_blocked FROM users";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Users - Mzansi Market</title>
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body>
  <div class="admin-layout">
    <header class="sticky-header">
      <div class="container header-container">
        <h1>Manage Users</h1>
        <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
      </div>
    </header>

    <div class="container admin-content">
      <aside class="admin-sidebar">
        <nav class="admin-nav">
          <a href="admin_users.php" class="nav-link active">Manage Users</a>
          <a href="admin_listings.php" class="nav-link">Manage Listings</a>
          <a href="admin_orders.php" class="nav-link">Manage Orders</a>
        </nav>
      </aside>

      <main class="admin-main">
        <section class="table-section">
          <div class="table-header">
            <h2>User List</h2>
            <input type="text" id="user-search" placeholder="Search users..." class="search-bar">
          </div>
          <table id="users-table">
            <thead>
              <tr>
                <th data-sort="id">ID <span class="sort-icon">↕</span></th>
                <th data-sort="username">Username <span class="sort-icon">↕</span></th>
                <th data-sort="email">Email <span class="sort-icon">↕</span></th>
                <th data-sort="status">Status <span class="sort-icon">↕</span></th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $row['id'] ?></td>
                  <td><?= htmlspecialchars($row['username']) ?></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= $row['is_blocked'] ? 'Blocked' : 'Active' ?></td>
                  <td>
                    <?php if (!$row['is_blocked']): ?>
                      <a class="action-link block" href="block_user.php?id=<?= $row['id'] ?>" data-action="block" data-id="<?= $row['id'] ?>">Block</a>
                    <?php else: ?>
                      <a class="action-link unblock" href="unblock_user.php?id=<?= $row['id'] ?>" data-action="unblock" data-id="<?= $row['id'] ?>">Unblock</a>
                    <?php endif; ?>
                    <a class="action-link delete" href="delete_user.php?id=<?= $row['id'] ?>" data-action="delete" data-id="<?= $row['id'] ?>">Delete</a>
                  </td>
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
      const searchInput = document.getElementById('user-search');
      const tableRows = document.querySelectorAll('#users-table tbody tr');

      searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        tableRows.forEach(row => {
          const username = row.cells[1].textContent.toLowerCase();
          const email = row.cells[2].textContent.toLowerCase();
          row.style.display = username.includes(searchTerm) || email.includes(searchTerm) ? '' : 'none';
        });
      });

      // Sorting functionality
      document.querySelectorAll('#users-table th[data-sort]').forEach(header => {
        header.addEventListener('click', () => {
          const sortKey = header.getAttribute('data-sort');
          const sortIcon = header.querySelector('.sort-icon');
          const isAsc = sortIcon.textContent === '↑' || sortIcon.textContent === '↕';
          
          tableRows.forEach(row => row.remove());
          const sortedRows = Array.from(tableRows).sort((a, b) => {
            let valA = a.cells[sortKey === 'id' ? 0 : sortKey === 'username' ? 1 : sortKey === 'email' ? 2 : 3].textContent;
            let valB = b.cells[sortKey === 'id' ? 0 : sortKey === 'username' ? 1 : sortKey === 'email' ? 2 : 3].textContent;
            
            if (sortKey === 'id') {
              valA = parseInt(valA);
              valB = parseInt(valB);
            }
            return isAsc ? valA > valB ? 1 : -1 : valA < valB ? 1 : -1;
          });

          document.querySelector('#users-table tbody').append(...sortedRows);
          document.querySelectorAll('.sort-icon').forEach(icon => icon.textContent = '↕');
          sortIcon.textContent = isAsc ? '↓' : '↑';
        });
      });

      // Confirmation modal
      document.querySelectorAll('.action-link').forEach(link => {
        link.addEventListener('click', (e) => {
          e.preventDefault();
          const action = link.getAttribute('data-action');
          const id = link.getAttribute('data-id');
          const modal = document.getElementById('confirmation-modal');
          const message = document.getElementById('modal-message');
          const confirmButton = modal.querySelector('.confirm');
          
          message.textContent = `Are you sure you want to ${action} this user?`;
          modal.classList.add('show');
          
          confirmButton.onclick = () => {
            window.location.href = link.getAttribute('href');
          };
          
          document.querySelector('.modal-button.cancel').onclick = () => {
            modal.classList.remove('show');
          };
        });
      });
    });
  </script>
</body>
</html>