<?php
include 'db.php';

$feedback = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST['email'];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $feedback = "<p class='success'>Registration successful. <a href='login.php'>Login</a></p>";
        } else {
            $feedback = "<p class='error'>Error: " . $conn->error . "</p>";
        }
    } else {
        $feedback = "<p class='error'>Passwords do not match.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - Mzansi Market</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body>
  <header class="sticky-header">
    <div class="container header-container">
      <h1>Create an Account</h1>
      <a href="index.php" class="back-link">← Back to Home</a>
    </div>
  </header>

  <main class="container">
    <form method="POST" class="form-container" id="register-form" novalidate>
      <h2>Create an Account</h2>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Email" required>
      </div>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Username" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrapper">
          <input type="password" id="password" name="password" placeholder="Password" required>
          <button type="button" class="toggle-password" aria-label="Toggle password visibility">👁</button>
        </div>
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <div class="input-wrapper">
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
          <button type="button" class="toggle-password" aria-label="Toggle password visibility">👁</button>
        </div>
      </div>
      <button type="submit" class="form-button">Register</button>
      <p class="form-link">Already have an account? <a href="login.php">Login</a></p>
      <?= $feedback ?>
    </form>
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

      // Form validation
      const form = document.getElementById('register-form');
      form.addEventListener('submit', (e) => {
        const email = document.getElementById('email').value;
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
          e.preventDefault();
          alert('Please enter a valid email address.');
          return;
        }

        if (username.length < 3) {
          e.preventDefault();
          alert('Username must be at least 3 characters long.');
          return;
        }

        if (password.length < 6) {
          e.preventDefault();
          alert('Password must be at least 6 characters long.');
          return;
        }

        if (password !== confirmPassword) {
          e.preventDefault();
          alert('Passwords do not match.');
        }
      });
    });
  </script>
</body>
</html>