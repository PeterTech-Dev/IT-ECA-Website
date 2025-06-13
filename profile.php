<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Fetch total listings
$listings = $conn->query("SELECT COUNT(*) AS count FROM listings WHERE user_id = $user_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Profile - Mzansi Market</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body>
  <header class="sticky-header">
    <div class="container header-container">
      <h1>My Profile</h1>
      <a href="index.php" class="back-link">← Back to Home</a>
    </div>
  </header>

  <main class="container profile-container">
    <h2>Hello, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h2>
    
    <section class="profile-section">
      <h3>Profile Analytics</h3>
      <p><strong>Total Listings:</strong> <?= $listings['count'] ?></p>
    </section>

    <section class="profile-section">
      <h3>My Listings</h3>
      <p><a href="listings.php" class="action-link">View My Listings →</a></p>
    </section>

    <section class="profile-section">
      <h3>Change Password</h3>
      <form method="POST" action="change_password.php" id="change-password-form" novalidate>
        <div class="form-group">
          <label for="current_password">Current Password</label>
          <div class="input-wrapper">
            <input type="password" id="current_password" name="current_password" placeholder="Current Password" required>
            <button type="button" class="toggle-password" aria-label="Toggle password visibility">👁</button>
          </div>
        </div>
        <div class="form-group">
          <label for="new_password">New Password</label>
          <div class="input-wrapper">
            <input type="password" id="new_password" name="new_password" placeholder="New Password" required>
            <button type="button" class="toggle-password" aria-label="Toggle password visibility">👁</button>
          </div>
          <p class="password-strength" id="password-strength"></p>
        </div>
        <button type="submit" class="form-button">Change Password</button>
      </form>
    </section>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Password visibility toggle
      document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', () => {
          const input = button.previousElementSibling;
          input.type = input.type === 'password' ? 'text' : 'password';
          button.textContent = input.type === 'password' ? '👁' : '👁‍🗨';
        });
      });

      // Password strength indicator
      const newPasswordInput = document.getElementById('new_password');
      const strengthText = document.getElementById('password-strength');
      
      newPasswordInput.addEventListener('input', () => {
        const password = newPasswordInput.value;
        let strength = 'Weak';
        let color = 'var(--color-accent-terra)';
        
        if (password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) {
          strength = 'Strong';
          color = 'var(--color-accent-green)';
        } else if (password.length >= 6) {
          strength = 'Moderate';
          color = 'var(--color-accent-gold)';
        }
        
        strengthText.textContent = password ? `Password Strength: ${strength}` : '';
        strengthText.style.color = color;
      });

      // Form validation
      const form = document.getElementById('change-password-form');
      form.addEventListener('submit', (e) => {
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        
        if (currentPassword.length < 6 || newPassword.length < 6) {
          e.preventDefault();
          alert('Passwords must be at least 6 characters long.');
        }
      });
    });
  </script>
</body>
</html>