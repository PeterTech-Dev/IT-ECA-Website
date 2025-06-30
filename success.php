<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT c.quantity, l.id AS listing_id
           FROM cart c
           JOIN listings l ON c.listing_id = l.id
           WHERE c.user_id = ?"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$purchases = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($purchases) {
    $conn->begin_transaction();

    try {
        $dec = $conn->prepare(
            "UPDATE listings SET quantity = quantity - ?
             WHERE id = ? AND quantity >= ?"
        );

        foreach ($purchases as $row) {
            $qty  = (int)$row['quantity'];
            $lid  = (int)$row['listing_id'];
            $dec->bind_param("iii", $qty, $lid, $qty);
            $dec->execute();
        }
        $dec->close();

        $delCart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $delCart->bind_param("i", $user_id);
        $delCart->execute();
        $delCart->close();

        $conn->query("DELETE FROM listings WHERE quantity <= 0");

        $conn->commit();
    } catch (Throwable $e) {
        $conn->rollback();
        die("<p class='error'>Order finalisation failed. Please contact support.</p>");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Success • Mzansi Market</title>
  <!-- Re‑use global stylesheet so it stays perfectly on‑brand -->
  <link rel="stylesheet" href="main.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" />
</head>
<body>
  <!-- Header -->
  <header class="sticky-header">
    <div class="container header-container">
      <div class="logo">Mzansi Market</div>
      <div class="header-actions">
        <a href="index.php" class="action-button">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="profile.php" class="action-button">Profile</a>
        <?php else: ?>
          <a href="login.php" class="action-button">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Success Message -->
  <main class="container">
    <section class="message-section" style="max-width:480px;">
      <!-- Simple check‑mark icon (SVG) -->
      <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent-green)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:12px;">
        <path d="M20 6L9 17l-5-5"/>
      </svg>
      <h2 style="margin-bottom:12px; font-size:1.75rem;">Success!</h2>
      <p>Your action was completed successfully. Thank you for using <strong>Mzansi&nbsp;Market</strong>.</p>

      <div style="margin-top:24px; display:flex; gap:12px; flex-wrap:wrap; justify-content:center;">
        <a href="index.php" class="action-button" style="flex:1 1 150px; text-align:center;">Continue Shopping</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="profile.php" class="action-button" style="flex:1 1 150px; background:var(--color-bg-secondary);">View Profile</a>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <!-- Footer (lightweight) -->
  <footer>
    <div class="container footer-container">
      <p>© <?php echo date('Y'); ?> Mzansi Market.</p>
    </div>
  </footer>
</body>
</html>
