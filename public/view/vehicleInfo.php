<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: index.php');
    exit;
}

require '../../config/db.php';

$vin = $_GET['vin'] ?? null;
$vehicleInfo = null;
$sale = null;
$payments = 0;
$warranty = null;

if ($vin) {
    $stmt = $conn->prepare("
        SELECT * 
        FROM vehicle 
        WHERE vin = ?
        ");
    $stmt->execute([$vin]);
    $vehicleInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vehicleInfo) {
        $customer_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("
        SELECT *
        FROM sale
        WHERE vin = ? AND customer_id = ?
        ");
        $stmt->execute([$vin, $customer_id]);
        $sale = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sale) {
            $stmt = $conn->prepare("
                SELECT SUM(amount) as total_payments 
                FROM payment 
                WHERE sale_id = ?
                ");
            $stmt->execute([$sale['id']]);
            $payments = $stmt->fetch(PDO::FETCH_ASSOC)['total_payments'] ?? 0;

            $stmt = $conn->prepare("
                SELECT * 
                FROM 
                warranty WHERE sale_id = ? AND end_date >= CURDATE()
            ");
            $stmt->execute([$sale['id']]);
            $warranty = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>
<!DOCTYPE html>
<html>

<?php include('../../templates/head.php'); ?>

<body>
    <?php include('../../templates/customerHeader.php'); ?>
    <section class="main-content" style="padding: 20px;">
        <h1>Vehicle Information</h1>
        <?php if ($vehicleInfo): ?>
            <div>
                <h2><?php echo htmlspecialchars($vehicleInfo['YEAR']) . ' ' . htmlspecialchars($vehicleInfo['make']) . ' ' . htmlspecialchars($vehicleInfo['model']); ?></h2>

                <p><strong>VIN:</strong> <?php echo htmlspecialchars($vehicleInfo['vin']); ?></p>
                <p><strong>Color:</strong> <?php echo htmlspecialchars($vehicleInfo['color']); ?></p>
                <p><strong>Interior Color:</strong> <?php echo htmlspecialchars($vehicleInfo['interior_color']); ?></p>
                <p><strong>Miles:</strong> <?php echo htmlspecialchars($vehicleInfo['miles']); ?></p>
                <p><strong>Style:</strong> <?php echo htmlspecialchars($vehicleInfo['style']); ?></p>
                <p><strong>Condition:</strong> <?php echo htmlspecialchars($vehicleInfo['vehicle_condition']); ?></p>

                <?php if ($sale): ?>
                    <p><strong>Sale Price:</strong> $<?php echo htmlspecialchars(number_format($sale['sale_price'], 2)); ?></p>
                    <p><strong>Amount Paid:</strong> $<?php echo htmlspecialchars(number_format($sale['down_payment'] + $payments, 2)); ?></p>
                    <p><strong>Amount Left to Pay:</strong> $<?php echo htmlspecialchars(number_format($sale['financed_amount'] - $payments, 2)); ?></p>
                <?php endif; ?>

                <?php if ($warranty): ?>
                    <h3>Current Warranty</h3>
                    <p><strong>Policy Name:</strong> <?php echo htmlspecialchars($warranty['policy_name']); ?></p>
                    <p><strong>Items Covered:</strong> <?php echo htmlspecialchars($warranty['items_covered']); ?></p>
                    <p><strong>Monthly Cost:</strong> $<?php echo htmlspecialchars(number_format($warranty['monthly_cost'], 2)); ?></p>
                    <p><strong>Deductible:</strong> $<?php echo htmlspecialchars(number_format($warranty['deductible'], 2)); ?></p>
                    <p><strong>Start Date:</strong> <?php echo htmlspecialchars($warranty['start_date']); ?></p>
                    <p><strong>End Date:</strong> <?php echo htmlspecialchars($warranty['end_date']); ?></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>No vehicle information found.</p>
        <?php endif; ?>
    </section>

</body>

</html>
