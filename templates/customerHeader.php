<?php
include '../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$user_id = $_SESSION['user_id'] ?? null;
$current_vin = $_GET['vin'] ?? '';
$vehicles = [];

if ($user_id) {
    $stmt = $conn->prepare("
        SELECT v.make, v.model, s.vin 
        FROM sale s 
        JOIN vehicle v ON s.vin = v.vin 
        WHERE s.customer_id = ?
        ");
    $stmt->execute([$user_id]);
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<section id="heading">
  <header>
    <div class="navbar-container">
      <h1>Jones Auto</h1>
    </div>
    <div class="navbar-container">
      <nav>
        <a href="#" class="_page" onclick="checkVehicle(event, 'vehicleInfo.php')">View Vehicle Info</a>
        <a href="#" class="_page" onclick="checkVehicle(event, 'paymentHistory.php')">View Payment History</a>
        <a href="#" class="_page" onclick="checkVehicle(event, 'payment.php')">Make Payment</a>
      </nav>
    </div>
    <div class="navbar-container">
      <select name="vehicle" id="vehicle">
        <option value="">Select Vehicle</option>
        <?php foreach ($vehicles as $v): ?>
          <option value="<?= htmlspecialchars($v['vin']) ?>" <?= ($current_vin === $v['vin']) ? 'selected' : '' ?>> <?= htmlspecialchars($v['make']) ?> <?= htmlspecialchars($v['model']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </header>
</section>

<script>
  function checkVehicle(event, page) {
    var vin = document.getElementById('vehicle').value;
    if (!vin) {
      event.preventDefault();
      alert('Please select a vehicle first.');
    } else {
      window.location.href = page + '?vin=' + vin;
      event.preventDefault();
    }
  }
</script>
