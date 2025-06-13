<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  echo '<!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>My Listings - Mzansi Market</title>
      <link rel="stylesheet" href="main.css">
      <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  </head>
  <body>
      <div class="layout">
          <header class="sticky-header">
              <div class="container header-container">
                  <h1>My Listings</h1>
                  <a href="index.php" class="back-link">← Back to Home</a>
              </div>
          </header>
          <main class="container">
              <section class="message-section">
                  <p>You must <a href="login.php" class="action-link">login</a> or <a href="register.php" class="action-link">register</a> to view your listings.</p>
              </section>
          </main>
      </div>
  </body>
  </html>';
  exit();
}

$user_id = $_SESSION['user_id'];

// Fetch listings only by this user
$sql = "SELECT l.id, l.title, l.description, l.price, l.location, u.username 
        FROM listings l
        JOIN users u ON l.user_id = u.id
        WHERE l.user_id = ?
        ORDER BY l.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Listings - Mzansi Market</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body>
  <div class="layout">
    <header class="sticky-header">
      <div class="container header-container">
        <h1>My Listings</h1>
        <div class="header-actions">
          <a href="create_listing.php" class="form-button">+ Create Listing</a>
          <a href="index.php" class="back-link">← Back to Home</a>
        </div>
      </div>
    </header>

    <main class="container">
      <section class="listings-section">
        <div class="table-header">
          <h2>Your Listings</h2>
          <input type="text" id="listing-search" placeholder="Search listings..." class="search-bar">
        </div>
        <?php if ($result->num_rows > 0): ?>
          <div class="grid" id="listings-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
              <div class="card">
                <div class="info">
                  <h3><?= htmlspecialchars($row['title']) ?></h3>
                  <p class="price">R<?= number_format($row['price'], 2) ?></p>
                  <p class="description"><?= htmlspecialchars($row['description']) ?></p>
                  <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                  <p class="posted-by">Posted by <?= htmlspecialchars($row['username']) ?></p>
                  <div class="actions">
                    <a href="edit_listing.php?id=<?= $row['id'] ?>" class="action-button">Edit</a>
                    <a href="delete_listing.php?id=<?= $row['id'] ?>" class="action-button delete" data-action="delete" data-id="<?= $row['id'] ?>">Delete</a>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <p class="no-listings">You haven’t added any listings yet.</p>
        <?php endif; ?>
      </section>
    </main>
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
      const listingCards = document.querySelectorAll('#listings-grid .card');

      searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        listingCards.forEach(card => {
          const title = card.querySelector('h3').textContent.toLowerCase();
          const description = card.querySelector('.description').textContent.toLowerCase();
          const location = card.querySelector('p strong').nextSibling.textContent.toLowerCase();
          card.style.display = title.includes(searchTerm) || description.includes(searchTerm) || location.includes(searchTerm) ? '' : 'none';
        });
      });

      // Confirmation modal for delete
      document.querySelectorAll('.action-button.delete').forEach(link => {
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