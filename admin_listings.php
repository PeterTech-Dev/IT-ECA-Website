<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$result = $conn->query("SELECT l.id, l.title, l.price, u.username FROM listings l JOIN users u ON l.user_id = u.id ORDER BY l.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Listings - Mzansi Market</title>
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body>
  <div class="admin-layout">
    <header class="sticky-header">
      <div class="container header-container">
        <h1>Manage Listings</h1>
        <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
      </div>
    </header>

    <div class="container admin-content">
      <aside class="admin-sidebar">
        <nav class="admin-nav">
          <a href="admin_users.php" class="nav-link">Manage Users</a>
          <a href="admin_listings.php" class="nav-link active">Manage Listings</a>
          <a href="admin_orders.php" class="nav-link">Manage Orders</a>
        </nav>
      </aside>

      <main class="admin-main">
        <section class="table-section">
          <div class="table-header">
            <h2>Listing List</h2>
            <input type="text" id="listing-search" placeholder="Search listings..." class="search-bar">
          </div>
          <table id="listings-table">
            <thead>
              <tr>
                <th data-sort="id">ID <span class="sort-icon">↕</span></th>
                <th data-sort="title">Title <span class="sort-icon">↕</span></th>
                <th data-sort="price">Price <span class="sort-icon">↕</span></th>
                <th data-sort="username">Posted By <span class="sort-icon">↕</span></th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($listing = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $listing['id'] ?></td>
                  <td><?= htmlspecialchars($listing['title']) ?></td>
                  <td>R<?= number_format($listing['price'], 2) ?></td>
                  <td><?= htmlspecialchars($listing['username']) ?></td>
                  <td>
                    <a class="action-link delete" href="delete_listing.php?id=<?= $listing['id'] ?>" data-action="delete" data-id="<?= $listing['id'] ?>">Delete</a>
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
      const searchInput = document.getElementById('listing-search');
      const tableRows = document.querySelectorAll('#listings-table tbody tr');

      searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        tableRows.forEach(row => {
          const title = row.cells[1].textContent.toLowerCase();
          const username = row.cells[3].textContent.toLowerCase();
          row.style.display = title.includes(searchTerm) || username.includes(searchTerm) ? '' : 'none';
        });
      });

      // Sorting functionality
      document.querySelectorAll('#listings-table th[data-sort]').forEach(header => {
        header.addEventListener('click', () => {
          const sortKey = header.getAttribute('data-sort');
          const sortIcon = header.querySelector('.sort-icon');
          const isAsc = sortIcon.textContent === '↑' || sortIcon.textContent === '↕';
          
          tableRows.forEach(row => row.remove());
          const sortedRows = Array.from(tableRows).sort((a, b) => {
            let valA = a.cells[sortKey === 'id' ? 0 : sortKey === 'title' ? 1 : sortKey === 'price' ? 2 : 3].textContent;
            let valB = b.cells[sortKey === 'id' ? 0 : sortKey === 'title' ? 1 : sortKey === 'price' ? 2 : 3].textContent;
            
            if (sortKey === 'id') valA = parseInt(valA), valB = parseInt(valB);
            if (sortKey === 'price') valA = parseFloat(valA.replace('R', '')), valB = parseFloat(valB.replace('R', ''));
            return isAsc ? valA > valB ? 1 : -1 : valA < valB ? 1 : -1;
          });

          document.querySelector('#listings-table tbody').append(...sortedRows);
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
          
          message.textContent = `Are you sure you want to ${action} this listing?`;
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