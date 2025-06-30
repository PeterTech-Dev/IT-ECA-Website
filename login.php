<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';

$feedback = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = $_POST["identifier"];
    $password = $_POST["password"];
    $remember = isset($_POST["remember"]);

    $is_email = filter_var($identifier, FILTER_VALIDATE_EMAIL);
    $user_found = false;

    if ($is_email) {
        $stmt = $conn->prepare("SELECT id, username, password, is_admin, is_blocked FROM users WHERE email = ?");
        $stmt->bind_param("s", $identifier);
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, is_admin, is_blocked FROM users WHERE username = ?");
        $stmt->bind_param("s", $identifier);
    }

    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $username, $hash, $admin, $blocked);
        $stmt->fetch();
        $user_found = true;
    }

    if (!$user_found && !$is_email) {
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $identifier);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $username, $hash);
            $stmt->fetch();
            $admin = 1;
            $blocked = 0;
            $user_found = true;
        }
    }

    if ($user_found) {
        if ($blocked) {
            $feedback = "<p class='error'>Account is blocked.</p>";
        } elseif (password_verify($password, $hash)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            $_SESSION["is_admin"] = $admin;

            if ($remember && !$admin) {
                $token = bin2hex(random_bytes(32));
                $expiry = time() + (12 * 60 * 60);
                setcookie("remember_token", $token, $expiry, "/");

                $update = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $update->bind_param("si", $token, $id);
                $update->execute();
            }

            header("Location: index.php");
            exit();
        } else {
            $feedback = "<p class='error'>Invalid password.</p>";
        }
    } else {
        $feedback = "<p class='error'>User not found.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Mzansi Market</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body>
  <header class="sticky-header">
    <div class="container header-container">
      <h1>Login</h1>
      <a href="index.php" class="back-link">← Back to Home</a>
    </div>
  </header>

  <main class="container">
    <form method="POST" class="form-container" id="login-form" novalidate>
      <h2>Login</h2>
      <div class="form-group">
        <label for="identifier">Email or Username</label>
        <input type="text" id="identifier" name="identifier" placeholder="Email or Username" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-wrapper">
          <input type="password" id="password" name="password" placeholder="Password" required>
          <button type="button" class="toggle-password" aria-label="Toggle password visibility">👁</button>
        </div>
      </div>
      <div class="checkbox-group">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">Remember Me</label>
      </div>
      <button type="submit" class="form-button">Login</button>
      <p class="form-link">Don't have an account? <a href="register.php">Register</a></p>
      <?= $feedback ?>
    </form>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Password visibility toggle
      document.querySelector('.toggle-password').addEventListener('click', () => {
        const passwordInput = document.getElementById('password');
        passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
        const button = passwordInput.nextElementSibling;
        button.textContent = passwordInput.type === 'password' ? '👁' : '👁‍🗨';
      });

      // Form validation
      const form = document.getElementById('login-form');
      form.addEventListener('submit', (e) => {
        const identifier = document.getElementById('identifier').value;
        const password = document.getElementById('password').value;

        if (!identifier || password.length < 6) {
          e.preventDefault();
          alert('Please enter a valid email/username and a password with at least 6 characters.');
        }
      });
    });
  </script>
</body>
</html>