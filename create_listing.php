<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $location = $_POST['location'];
    $quantity = intval($_POST['quantity']);
    $weight = floatval($_POST['weight']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO listings (title, description, price, location, quantity, weight, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsddi", $title, $description, $price, $location, $quantity, $weight, $user_id);
    $stmt->execute();

    header('Location: listings.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Listing - Mzansi Market</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body>
  <header class="sticky-header">
    <div class="container header-container">
      <h1>Create a New Listing</h1>
      <a href="index.php" class="back-link">← Back to Home</a>
    </div>
  </header>

  <main class="container">
    <form action="create_listing.php" method="POST" class="form-container" id="create-listing-form" novalidate>
      <h2>Create Listing</h2>
      <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" placeholder="Title" required>
      </div>
      <div class="form-group">
        <label for="description">Description (max 500 characters)</label>
        <textarea id="description" name="description" placeholder="Description" required maxlength="500"></textarea>
        <p class="char-counter" id="char-counter">0/500</p>
      </div>
      <div class="form-group">
        <label for="price">Price (R)</label>
        <input type="number" id="price" name="price" placeholder="Price (R)" step="0.01" min="0" required>
      </div>
      <div class="form-group">
        <label for="location">Location</label>
        <input type="text" id="location" name="location" placeholder="Location" required>
      </div>
      <div class="form-group">
        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" name="quantity" placeholder="Quantity" min="1" required>
      </div>
      <div class="form-group">
        <label for="weight">Weight (kg)</label>
        <input type="number" id="weight" name="weight" placeholder="Weight (kg)" step="0.1" min="0" required>
      </div>
      <div class="form-group">
        <label for="image">Image (Optional)</label>
        <input type="file" id="image" name="image" accept="image/*">
        <div class="image-preview" id="image-preview"></div>
      </div>
      <button type="submit" class="form-button">Create Listing</button>
    </form>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Character counter for description
      const description = document.getElementById('description');
      const charCounter = document.getElementById('char-counter');
      
      description.addEventListener('input', () => {
        const count = description.value.length;
        charCounter.textContent = `${count}/500`;
        charCounter.style.color = count > 450 ? 'var(--color-accent-terra)' : 'var(--color-text-secondary)';
      });

      // Image preview (placeholder for future file upload)
      const imageInput = document.getElementById('image');
      const imagePreview = document.getElementById('image-preview');
      
      imageInput.addEventListener('change', () => {
        imagePreview.innerHTML = '';
        if (imageInput.files && imageInput.files[0]) {
          const reader = new FileReader();
          reader.onload = (e) => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '200px';
            img.style.borderRadius = '8px';
            imagePreview.appendChild(img);
          };
          reader.readAsDataURL(imageInput.files[0]);
        }
      });

      // Form validation
      const form = document.getElementById('create-listing-form');
      form.addEventListener('submit', (e) => {
        const title = document.getElementById('title').value;
        const description = document.getElementById('description').value;
        const price = document.getElementById('price').value;
        const quantity = document.getElementById('quantity').value;
        const weight = document.getElementById('weight').value;

        if (title.length < 3 || description.length < 10 || price <= 0 || quantity < 1 || weight < 0) {
          e.preventDefault();
          alert('Please ensure: Title is at least 3 characters, Description is at least 10 characters, Price is positive, Quantity is at least 1, and Weight is non-negative.');
        }
      });
    });
  </script>
</body>
</html>