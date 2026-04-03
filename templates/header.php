<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_type = $_SESSION['user_type'] ?? null;
?>
<section id="heading">
  <header>
    <div class="navbar-container">
      <h1 href="../public/index.php">Jones Auto</h1>
    </div>
    <div class="navbar-container">
      <nav>
        <?php if ($user_type === 'customer'): ?>
          <a href="../public/customer.php" class="_page">View Vehicle Info</a>
          <a href="../public/paymentHistory.php" class="_page">View Payment History</a>
          <a href="../public/payment.php" class="_page">Make Payment</a>
        <?php elseif ($user_type === 'employee'): ?>
          <a href="../public/purchaseCar.php" class="_page">Purchase Vehicle</a>
          <a href="../public/addCustomer.php" class="_page">Add Customer</a>
          <a href="../public/carSales.php" class="_page">Sell Vehicle</a>
          <a href="../public/warranty.php" class="_page">Add Warranty</a>
          <a href="../public/inventory.php" class="_page">View Inventory</a>
        <?php elseif ($user_type === 'admin'): ?>
          <a href="../public/purchaseCar.php" class="_page">Purchase Vehicle</a>
          <a href="../public/addCustomer.php" class="_page">Add Customer</a>
          <a href="../public/carSales.php" class="_page">Sell Vehicle</a>
          <a href="../public/warranty.php" class="_page">Add Warranty</a>
          <a href="../public/inventory.php" class="_page">View Inventory</a>
          <a href="../public/report.php" class="_page">Monthly Report</a>
        <?php else: ?>
          <a href="../public/index.php" class="_page">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
</section>