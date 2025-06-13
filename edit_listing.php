<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    echo "Listing ID missing.";
    exit();
}

$listing_id = intval($_GET['id']);

// Fetch listing
$stmt = $conn->prepare("SELECT * FROM listings WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $listing_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Listing not found or you don't have permission to edit this listing.";
    exit();
}

$listing = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $location = $_POST['location'];
    $quantity = intval($_POST['quantity']);
    $weight = floatval($_POST['weight']);

    $update = $conn->prepare("UPDATE listings SET title = ?, description = ?, price = ?, location = ?, quantity = ?, weight = ? WHERE id = ? AND user_id = ?");
    $update->bind_param("ssdsiidi", $title, $description, $price, $location, $quantity, $weight, $listing_id, $_SESSION['user_id']);
    $update->execute();

    header("Location: listings.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Listing - Mzansi Market</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body>
  <div class="layout">
    <header class="sticky-header">
      <div class="container header-container">
        <h1>Edit Listing</h1>
        <a href="listings.php" class="back-link">← Back to My Listings</a>
      </div>
    </header>

    <main class="container">
      <form method="POST" class="form-container" id="edit-listing-form" novalidate>
        <h2>Edit Your Listing</h2>
        <div class="form-group">
          <label for="title">Title</label>
          <input type="text" id="title" name="title" value="<?= htmlspecialchars($listing['title']) ?>" placeholder="Title" required>
        </div>
        <div class="form-group">
          <label for="description">Description (max 500 characters)</label>
          <textarea id="description" name="description" placeholder="Description" required maxlength="500"><?= htmlspecialchars($listing['description']) ?></textarea>
          <p class="char-counter" id="char-counter"><?= strlen($listing['description']) ?>/500</p>
        </div>
        <div class="form-group">
          <label for="price">Price (R)</label>
          <input type="number" id="price" name="price" value="<?= htmlspecialchars($listing['price']) ?>" placeholder="Price (R)" step="0.01" min="0" required>
        </div>
        <div class="form-group">
          <label for="location">Location</label>
          <input type="text" id="location" name="location" value="<?= htmlspecialchars($listing['location']) ?>" placeholder="Location" required>
        </div>
        <div class="form-group">
          <label for="quantity">Quantity</label>
          <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($listing['quantity']) ?>" placeholder="Quantity" min="1" required>
        </div>
        <div class="form-group">
          <label for="weight">Weight (kg)</label>
          <input type="number" id="weight" name="weight" value="<?= htmlspecialchars($listing['weight']) ?>" placeholder="Weight (kg)" step="0.1" min="0" required>
        </div>
        <div class="form-group">
          <label for="image">Image (Optional)</label>
          <input type="file" id="image" name="image" accept="image/*">
          <div class="image-preview" id="image-preview"></div>
        </div>
        <button type="submit" class="form-button">Update Listing</button>
      </form>
    </main>
  </div>

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

      // Image preview
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
      const form = document.getElementById('edit-listing-form');
      form.addEventListener('submit', (e) => {
        const title = document.getElementById('title').value;
        const description = document.getElementById('description').value;
        const price = document.getElementById('price').value;
        const location = document.getElementById('location').value;
        const quantity = document.getElementById('quantity').value;
        const weight = document.getElementById('weight').value;

        if (title.length < 3) {
          e.preventDefault();
          alert('Title must be at least 3 characters long.');
          return;
        }
        if (description.length < 10) {
          e.preventDefault();
          alert('Description must be at least 10 characters long.');
          return;
        }
        if (price <= 0) {
          e.preventDefault();
          alert('Price must be a positive number.');
          return;
        }
        if (location.length < 3) {
          e.preventDefault();
          alert('Location must be at least 3 characters long.');
          return;
        }
        if (quantity < 1) {
          e.preventDefault();
          alert('Quantity must be at least 1.');
          return;
        }
        if (weight < 0) {
          e.preventDefault();
          alert('Weight must be non-negative.');
        }
      });
    });
  </script>
</body>
</html>