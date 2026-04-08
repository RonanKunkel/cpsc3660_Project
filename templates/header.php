<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_type = $_SESSION['user_type'] ?? null;

$home_link = '../public/index.php';
if ($user_type === 'customer') {
    $home_link = '../../public/view/customerHome.php';
} elseif ($user_type === 'employee') {
    $home_link = '../../public/view/employeeHome.php';
} elseif ($user_type === 'admin') {
    $home_link = '../../public/view/adminHome.php';
}
?>
<section id="heading">
  <header>
    <div class="navbar-container">
      <h1>Jones Auto</h1>
    </div>
    <div class="navbar-container">
      <nav>
        <?php if ($user_type === 'customer'): ?>
          <a href="../../public/view/customerHome.php" class="_page">Customer Home</a>
          <a href="../../public/view/paymentHistoryView.php" class="_page">Payment History</a>
          <a href="../../public/entry/makePayment.php" class="_page">Make Payment</a>
        <?php elseif ($user_type === 'employee'): ?>
          <a href="../../public/view/employeeHome.php" class="_page">Employee Home</a>
          <a href="../../public/entry/purchaseCar.php" class="_page">Purchase Vehicle</a>
          <a href="../../public/entry/addCustomer.php" class="_page">Add Customer</a>
          <a href="../../public/entry/carSales.php" class="_page">Sell Vehicle</a>
          <a href="../../public/entry/warranty.php" class="_page">Add Warranty</a>
          <a href="../../public/view/inventoryView.php" class="_page">Inventory</a>
          <a href="../../public/view/customerEmployersView.php" class="_page">See 'x' Customer Employers</a>
          <a href="../../public/entry/payments.php" class="_page">Create Payment</a>
        <?php elseif ($user_type === 'admin'): ?>
          <a href="../../public/entry/purchaseCar.php" class="_page">Purchase Vehicle</a>
          <a href="../../public/entry/addCustomer.php" class="_page">Add Customer</a>
          <a href="../../public/entry/addEmployee.php" class="_page">Add Employee</a>
          <a href="../../public/entry/carSales.php" class="_page">Sell Vehicle</a>
          <a href="../../public/entry/warranty.php" class="_page">Add Warranty</a>
          <a href="../../public/view/inventoryView.php" class="_page">Inventory</a>
          <a href="../../public/view/report.php" class="_page">Monthly Report</a>
          <a href="../../public/entry/payments.php" class="_page">Create Payment</a>
          <a href="../../public/view/paymentHistoryView.php" class="_page">Payment History</a>
          <a href="../../public/view/vehicleInfo.php" class="_page">Vehicle Info</a>
          <a href="../../public/view/vehiclesView.php" class="_page">Vehicles</a>
          <a href="../../public/view/customersView.php" class="_page">Customers</a>
          <a href="../../public/view/customerEmployersView.php" class="_page">See 'x' Customer Employers</a>
        <?php else: ?>
          <a href="/public/index.php" class="_page">Login</a>
        <?php endif; ?>
        <?php if ($user_type): ?>
          <a href="../index.php?logout=1" class="_page">Logout</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
</section>
