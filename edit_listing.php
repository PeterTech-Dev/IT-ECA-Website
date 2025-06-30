<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die("No listing ID provided.");
}

$stmt = $conn->prepare("SELECT * FROM listings WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$listing = $result->fetch_assoc();

if (!$listing) {
    die("Listing not found or unauthorized.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $location = $_POST['location'];
    $quantity = intval($_POST['quantity']);
    $weight = floatval($_POST['weight']);
    $category = $_POST["category"];
    $image_path = $_POST["current_image"];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old image if it's not empty and exists
            if (!empty($listing['image_path']) && file_exists($listing['image_path'])) {
                unlink($listing['image_path']);
            }
            $image_path = $target_file;
        } else {
            echo "Failed to upload new image.";
            exit();
        }
    }

    $stmt = $conn->prepare("UPDATE listings SET title=?, description=?, price=?, location=?, quantity=?, weight=?, image_path=?, category=? WHERE id=?");
    $stmt->bind_param("ssdssdssi", $title, $description, $price, $location, $quantity, $weight, $image_path, $category, $id);
    $stmt->execute();

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
  <header class="sticky-header">
    <div class="container header-container">
      <h1>Edit Listing</h1>
      <a href="index.php" class="back-link">← Back to Home</a>
    </div>
  </header>

  <main class="container">
    <form action="edit_listing.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data" class="form-container" id="edit-listing-form" novalidate>
      <h2>Edit Listing</h2>
      <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($listing['title']) ?>" required>
      </div>
      <div class="form-group">
        <label for="description">Description (max 500 characters)</label>
        <textarea id="description" name="description" maxlength="500" required><?= htmlspecialchars($listing['description']) ?></textarea>
        <p class="char-counter" id="char-counter">0/500</p>
      </div>
      <div class="form-group">
        <label for="price">Price (R)</label>
        <input type="number" id="price" name="price" value="<?= $listing['price'] ?>" step="0.01" min="0" required>
      </div>
      <div class="form-group">
        <label for="location">Location</label>
        <input type="text" id="location" name="location" value="<?= htmlspecialchars($listing['location']) ?>" required>
      </div>
      <div class="form-group">
        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" name="quantity" value="<?= $listing['quantity'] ?>" min="1" required>
      </div>
      <div class="form-group">
        <label for="weight">Weight (kg)</label>
        <input type="number" id="weight" name="weight" value="<?= $listing['weight'] ?>" step="0.1" min="0" required>
      </div>
      <div class="form-group">
        <label>Category:</label><br>
        <select name="category" required>
          <option value="">Select a category</option>
          <?php
          $categories = [
            "Electronics", "Clothing", "Books", "Home & Kitchen", "Beauty & Personal Care", "Health & Wellness",
            "Toys & Games", "Sports & Outdoors", "Automotive", "Pet Supplies", "Jewelry & Accessories", "Shoes",
            "Office Supplies", "Tools & DIY", "Music & Instruments", "Movies & TV", "Groceries", "Garden & Outdoors",
            "Baby Products", "Art & Crafts", "Collectibles", "Mobile Phones", "Tablets & Accessories",
            "Computer Accessories", "Gaming", "Watches", "Luggage & Travel"
          ];
          foreach ($categories as $cat) {
              $selected = ($listing['category'] === $cat) ? 'selected' : '';
              echo "<option value=\"$cat\" $selected>$cat</option>";
          }
          ?>
        </select>
      </div>
      <div class="form-group">
        <label>Image:</label><br>
        <?php if ($listing['image_path']): ?>
          <img src="<?= $listing['image_path'] ?>" alt="Current Image" style="max-width: 150px; border-radius: 8px;"><br>
        <?php endif; ?>
        <input type="hidden" name="current_image" value="<?= htmlspecialchars($listing['image_path']) ?>">
        <input type="file" name="image" id="image" accept="image/*"><br>
        <div id="image-preview" class="image-preview"></div><br>
      </div>

      <button type="submit" class="form-button">Update Listing</button>
    </form>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const description = document.getElementById('description');
      const charCounter = document.getElementById('char-counter');
      charCounter.textContent = `${description.value.length}/500`;

      description.addEventListener('input', () => {
        const count = description.value.length;
        charCounter.textContent = `${count}/500`;
        charCounter.style.color = count > 450 ? 'var(--color-accent-terra)' : 'var(--color-text-secondary)';
      });

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

      const form = document.getElementById('edit-listing-form');
      form.addEventListener('submit', (e) => {
        const title = document.getElementById('title').value;
        const desc = document.getElementById('description').value;
        const price = document.getElementById('price').value;
        const quantity = document.getElementById('quantity').value;
        const weight = document.getElementById('weight').value;

        if (title.length < 3 || desc.length < 10 || price <= 0 || quantity < 1 || weight < 0) {
          e.preventDefault();
          alert('Please ensure: Title is at least 3 characters, Description is at least 10 characters, Price is positive, Quantity is at least 1, and Weight is non-negative.');
        }
      });
    });
  </script>
</body>
</html>
