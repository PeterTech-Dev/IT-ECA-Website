<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mzansi Market</title>
  <link rel="stylesheet" href="main.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" />
</head>
<body>
  <!-- Header -->
  <header class="sticky-header">
    <div class="container header-container">
      <div class="logo">Mzansi Market</div>
      <input type="text" placeholder="Search for items..." class="search-bar" aria-label="Search" />
      <div class="user-actions">
        <!-- Profile Dropdown -->
        <div class="profile-menu">
          <button class="profile-btn" aria-haspopup="true">Profile ▼</button>
          <div class="dropdown">
            <?php if (!isset($_SESSION['user_id'])): ?>
              <a href="login.php">Login</a>
              <a href="register.php">Register</a>
            <?php else: ?>
              <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <a href="admin_dashboard.php">Dashboard</a>
              <?php endif; ?>
              <a href="profile.php">My Profile</a>
              <a href="logout.php">Logout</a>
            <?php endif; ?>
          </div>
        </div>
        <!-- Cart Trigger -->
        <div id="cd-cart-trigger">
          <a class="cd-img-replace" href="#0" aria-label="Cart">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M6.29977 5H21L19 12H7.37671M20 16H8L6 3H3M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Cart Sidebar -->
  <div id="cd-shadow-layer"></div>
  <div id="cd-cart">
    <div class="cart-header">
      <h2>Cart</h2>
      <button class="close-cart" aria-label="Close cart">✕</button>
    </div>
    <ul class="cd-cart-items"></ul>
    <div class="cd-cart-total">
      <p>Total <span id="cart-total">R0.00</span></p>
    </div>
    <a href="checkout.php" class="checkout-btn">Checkout</a>
  </div>

  <!-- Category Navigation -->
  <nav class="categories">
    <div class="container">
      <button data-category="all">All</button>
      <button data-category="vehicles">Vehicles</button>
      <button data-category="electronics">Electronics</button>
      <button data-category="property">Property</button>
      <button data-category="home & garden">Home & Garden</button>
      <button data-category="clothing">Clothing</button>
    </div>
  </nav>

<?php
  include 'db.php';
  $sql = "SELECT l.title, l.description, l.price, l.location, l.weight, l.quantity, u.username 
          FROM listings l
          JOIN users u ON l.user_id = u.id
          ORDER BY l.id DESC";
  $result = $conn->query($sql);
?>

  <main class="container grid">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
          <img src="https://dummyimage.com/300x200/2a2a2a/ffffff&text=Listing" alt="Listing" loading="lazy" />
          <div class="info">
            <h3><?= htmlspecialchars($row['title']) ?></h3>
            <p class="price">R<?= htmlspecialchars($row['price']) ?></p>
            <p class="description"><?= htmlspecialchars($row['description']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
            <p><strong>Weight:</strong> <?= htmlspecialchars($row['weight']) ?> kg</p>
            <p><strong>Available:</strong> <?= htmlspecialchars($row['quantity']) ?></p>
            <p class="posted-by"><em>Posted by <?= htmlspecialchars($row['username']) ?></em></p>
            <div class="actions">
              <button class="add-cart-btn">Add to Cart</button>
              <button class="buy-now-btn">Buy Now</button>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="no-listings">No listings available yet.</p>
    <?php endif; ?>
  </main>

  <!-- Footer -->
  <footer>
    <div class="container footer-container">
      <div class="footer-links">
        <a href="#">About Us</a>
        <a href="#">Contact</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Help</a>
      </div>
      <div class="social-links">
        <a href="#" aria-label="Facebook"><svg class="social-icon" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg></a>
        <a href="#" aria-label="Twitter"><svg class="social-icon" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-2.717 0-4.92 2.203-4.92 4.917 0 .39.045.765.127 1.124-4.09-.205-7.719-2.165-10.141-5.144-.424.722-.666 1.561-.666 2.457 0 1.695.906 3.189 2.283 4.062-.839-.026-1.629-.256-2.32-.759v.065c0 2.368 1.684 4.339 3.918 4.785-.41.111-.843.171-1.287.171-.314 0-.615-.03-.916-.086.621 1.936 2.417 3.345 4.545 3.385-1.667 1.306-3.767 2.083-6.051 2.083-.393 0-.779-.022-1.161-.067 2.155 1.382 4.717 2.188 7.468 2.188 8.965 0 13.863-7.428 13.863-13.863 0-.211-.005-.422-.014-.632.951-.689 1.781-1.55 2.437-2.532z"/></svg></a>
        <a href="#" aria-label="Instagram"><svg class="social-icon" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.982-6.98.058-1.281.072-1.689.072-4.948 0-3.259-.014-3.667-.072-4.948-.2-4.354-2.618-6.782-6.98-6.982-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
      </div>
      <p>© 2025 Mzansi Market. Proudly built in South Africa 🇿🇦</p>
    </div>
  </footer>

  <script>
    let cart = [];

    document.addEventListener('DOMContentLoaded', () => {
      const cartTrigger = document.getElementById('cd-cart-trigger');
      const cartPanel = document.getElementById('cd-cart');
      const shadowLayer = document.getElementById('cd-shadow-layer');
      const searchInput = document.querySelector(".search-bar");
      const cards = document.querySelectorAll(".card");
      const closeCart = document.querySelector(".close-cart");

      // Open cart panel
      cartTrigger.addEventListener('click', (event) => {
        event.preventDefault();
        cartPanel.classList.add('speed-in');
        shadowLayer.classList.add('is-visible');
        document.body.classList.add('overflow-hidden');
      });

      // Close cart panel
      shadowLayer.addEventListener('click', () => {
        cartPanel.classList.remove('speed-in');
        shadowLayer.classList.remove('is-visible');
        document.body.classList.remove('overflow-hidden');
      });

      // Close cart with button
      closeCart.addEventListener('click', () => {
        cartPanel.classList.remove('speed-in');
        shadowLayer.classList.remove('is-visible');
        document.body.classList.remove('overflow-hidden');
      });

      // Search Functionality
      searchInput.addEventListener("input", () => {
        const searchTerm = searchInput.value.toLowerCase();
        cards.forEach(card => {
          const title = card.querySelector("h3").textContent.toLowerCase();
          card.style.display = title.includes(searchTerm) ? "block" : "none";
        });
      });

      // Filter by category
      document.querySelectorAll(".categories button").forEach(button => {
        button.addEventListener("click", () => {
          const category = button.getAttribute("data-category").toLowerCase();
          cards.forEach(card => {
            const title = card.querySelector("h3").textContent.toLowerCase();
            card.style.display = category === "all" || title.includes(category) ? "block" : "none";
          });
          document.querySelectorAll(".categories button").forEach(btn => btn.classList.remove("active"));
          button.classList.add("active");
        });
      });

      // Add to Cart buttons
      document.querySelectorAll(".add-cart-btn").forEach(button => {
        button.addEventListener("click", () => {
          const card = button.closest(".card");
          const title = card.querySelector("h3").textContent;
          const priceText = card.querySelector(".price").textContent.replace(/[^\d.]/g, '');
          const price = parseFloat(priceText);

          const existing = cart.find(item => item.title === title);
          if (existing) {
            existing.qty += 1;
          } else {
            cart.push({ title, price, qty: 1 });
          }

          updateCartDisplay();
        });
      });
    });

    // Update cart display
    function updateCartDisplay() {
      const cartList = document.querySelector('.cd-cart-items');
      const cartTotal = document.getElementById('cart-total');
      cartList.innerHTML = '';
      let total = 0;

      cart.forEach((item, index) => {
        total += item.price * item.qty;
        const li = document.createElement('li');
        li.innerHTML = `
          <div class="cart-item-content">
            <span class="cd-qty">${item.qty}x</span>
            <span class="cd-title">${item.title}</span>
            <span class="cd-price">R${(item.price * item.qty).toFixed(2)}</span>
            <a href="#0" class="cd-item-remove cd-img-replace" onclick="removeItem(${index})">Remove</a>
          </div>
        `;
        cartList.appendChild(li);
      });

      cartTotal.textContent = `R${total.toFixed(2)}`;
      saveCartToSession();
    }

    function saveCartToSession() {
      const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
      fetch('save_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          items: cart,
          total: total.toFixed(2)
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          console.log("Cart saved to session.");
        } else {
          console.error("Failed to save cart:", data.error);
        }
      });
    }

    function changeQty(index, delta) {
      cart[index].qty += delta;
      if (cart[index].qty <= 0) {
        cart.splice(index, 1);
      }
      updateCartDisplay();
    }

    function removeItem(index) {
      cart.splice(index, 1);
      updateCartDisplay();
    }
  </script>
</body>
</html>