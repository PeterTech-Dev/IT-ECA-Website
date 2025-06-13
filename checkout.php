<?php
session_start();

$cart = (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) ? $_SESSION['cart'] : ['items' => [], 'total' => 0];

// PayFast Setup
$merchant_id = "10000100";
$merchant_key = "46f0cd694581a";
$return_url = "http://localhost/ITECA_Project/success.php";
$cancel_url = "http://localhost/ITECA_Project/cancel.php";
$notify_url = "http://localhost/ITECA_Project/notify.php";
$item_name = "Order from Mzansi Market";

// Calculate shipping fee
$shipping_fee = 0;
$total_weight = 0;

foreach ($cart['items'] as $item) {
    $total_weight += $item['weight'] * $item['qty'];
}

// Weight + Price Based Shipping Logic
if ($total_weight <= 2) {
    $shipping_fee = $cart['total'] < 500 ? 60 : ($cart['total'] < 1000 ? 40 : 20);
} elseif ($total_weight <= 5) {
    $shipping_fee = $cart['total'] < 500 ? 80 : ($cart['total'] < 1000 ? 60 : 40);
} else {
    $shipping_fee = $cart['total'] < 500 ? 100 : ($cart['total'] < 1000 ? 80 : 60);
}

$amount = number_format($cart['total'] + $shipping_fee, 2, '.', '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Checkout - Mzansi Market</title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
</head>
<body>
  <div class="layout">
    <header class="sticky-header">
      <div class="container header-container">
        <h1>Checkout</h1>
        <a href="index.php" class="back-link">← Back to Home</a>
      </div>
    </header>

    <main class="container">
      <section class="checkout-section">
        <?php if (count($cart['items']) > 0): ?>
          <div class="checkout-layout">
            <div class="summary-section">
              <h2>Order Summary</h2>
              <ul class="order-list">
                <?php foreach ($cart['items'] as $item): ?>
                  <li>
                    <span class="order-item"><?= $item['qty'] ?>x <?= htmlspecialchars($item['title']) ?></span>
                    <span class="order-price">R<?= number_format($item['price'] * $item['qty'], 2) ?></span>
                    <span class="order-weight">(<?= number_format($item['weight'], 2) ?>kg each)</span>
                  </li>
                <?php endforeach; ?>
              </ul>
              <div class="summary-details">
                <p><strong>Subtotal:</strong> <span>R<?= number_format($cart['total'], 2) ?></span></p>
                <p><strong>Total Weight:</strong> <span><?= number_format($total_weight, 2) ?> kg</span></p>
                <p><strong>Shipping:</strong> <span>R<?= number_format($shipping_fee, 2) ?></span></p>
                <p><strong>Total With Shipping:</strong> <span>R<?= $amount ?></span></p>
              </div>
            </div>
            <div class="divider"></div>
            <div class="form-section">
              <form action="https://sandbox.payfast.co.za/eng/process" method="post" class="form-container" id="payfast-form" novalidate>
                <h3>Billing Details</h3>
                <div class="form-grid">
                  <div class="form-group">
                    <label for="name_first">First Name</label>
                    <input type="text" id="name_first" name="name_first" placeholder="First Name" required>
                  </div>
                  <div class="form-group">
                    <label for="name_last">Last Name</label>
                    <input type="text" id="name_last" name="name_last" placeholder="Last Name" required>
                  </div>
                  <div class="form-group">
                    <label for="email_address">Email Address</label>
                    <input type="email" id="email_address" name="email_address" placeholder="Email Address" required>
                  </div>
                  <div class="form-group">
                    <label for="address_street">Street Address</label>
                    <input type="text" id="address_street" name="address_street" placeholder="Street Address">
                  </div>
                  <div class="form-group">
                    <label for="address_city">City</label>
                    <input type="text" id="address_city" name="address_city" placeholder="City">
                  </div>
                  <div class="form-group">
                    <label for="address_country">Country</label>
                    <input type="text" id="address_country" name="address_country" value="South Africa" readonly>
                  </div>
                </div>
                <input type="hidden" name="merchant_id" value="<?= $merchant_id ?>">
                <input type="hidden" name="merchant_key" value="<?= $merchant_key ?>">
                <input type="hidden" name="return_url" value="<?= $return_url ?>">
                <input type="hidden" name="cancel_url" value="<?= $cancel_url ?>">
                <input type="hidden" name="notify_url" value="<?= $notify_url ?>">
                <input type="hidden" name="amount" value="<?= $amount ?>">
                <input type="hidden" name="item_name" value="<?= $item_name ?>">
                <button type="submit" class="form-button">Proceed to Secure Payment</button>
              </form>
            </div>
          </div>
        <?php else: ?>
          <p class="no-listings">Your cart is empty.</p>
        <?php endif; ?>
      </section>
    </main>
  </div>

  <div class="modal" id="confirmation-modal">
    <div class="modal-content">
      <p id="modal-message">Are you sure you want to proceed with the payment?</p>
      <div class="modal-actions">
        <button class="modal-button confirm">Confirm</button>
        <button class="modal-button cancel">Cancel</button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('payfast-form');
      const modal = document.getElementById('confirmation-modal');
      const confirmButton = modal.querySelector('.confirm');
      const cancelButton = modal.querySelector('.cancel');

      form.addEventListener('submit', (e) => {
        e.preventDefault();
        const firstName = document.getElementById('name_first').value.trim();
        const lastName = document.getElementById('name_last').value.trim();
        const email = document.getElementById('email_address').value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (firstName.length < 2) {
          alert('First Name must be at least 2 characters long.');
          return;
        }
        if (lastName.length < 2) {
          alert('Last Name must be at least 2 characters long.');
          return;
        }
        if (!emailRegex.test(email)) {
          alert('Please enter a valid email address.');
          return;
        }

        modal.classList.add('show');
        confirmButton.onclick = () => {
          form.submit();
        };
        cancelButton.onclick = () => {
          modal.classList.remove('show');
        };
      });
    });
  </script>
</body>
</html>