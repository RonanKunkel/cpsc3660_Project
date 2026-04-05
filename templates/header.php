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
          <a href="../../public/view/customerHome.php" class="_page">Customer Home</a>
          <a href="../../public/view/paymentHistoryView.php" class="_page">View Payment History</a>
          <a href="../../public/entry/makePayment.php" class="_page">Make Payment</a>
        <?php elseif ($user_type === 'employee'): ?>
          <a href="../../public/view/employeeHome.php" class="_page">Employee Home</a>
          <a href="../../public/entry/purchaseCar.php" class="_page">Purchase Vehicle</a>
          <a href="../../public/entry/addCustomer.php" class="_page">Add Customer</a>
          <a href="../../public/entry/carSales.php" class="_page">Sell Vehicle</a>
          <a href="../../public/entry/warranty.php" class="_page">Add Warranty</a>
          <a href="../../public/view/inventoryView.php" class="_page">View Inventory</a>
        <?php elseif ($user_type === 'admin'): ?>
          <a href="../../public/entry/purchaseCar.php" class="_page">Purchase Vehicle</a>
          <a href="../../public/entry/addCustomer.php" class="_page">Add Customer</a>
          <a href="../../public/entry/addEmployee.php" class="_page">Add Employee</a>
          <a href="../../public/entry/carSales.php" class="_page">Sell Vehicle</a>
          <a href="../../public/entry/warranty.php" class="_page">Add Warranty</a>
          <a href="../../public/view/inventoryView.php" class="_page">View Inventory</a>
          <a href="../../public/view/report.php" class="_page">Monthly Report</a>
          <a href="../../public/entry/payments.php" class="_page">Create Payment</a>
        <?php else: ?>
          <a href="/public/index.php" class="_page">Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
</section>
