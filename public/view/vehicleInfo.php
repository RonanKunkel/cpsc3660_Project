<?php
session_start();
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['customer', 'admin'])) {
    header('Location: ../index.php');
    exit;
}

require '../../config/db.php';

$vin = $_GET['vin'] ?? null;
$vehicleInfo = null;
$sale = null;
$payments = 0;
$warranty = null;
$repairs = [];
$purchase = null;

if ($_SESSION['user_type'] === 'admin') {
    if ($vin) {
        $stmt = $conn->prepare("
            SELECT * 
            FROM vehicle 
            WHERE vin = ?
            ");
        $stmt->execute([$vin]);
        $vehicleInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($vehicleInfo) {
            $stmt = $conn->prepare("
            SELECT *
            FROM sale
            WHERE vin = ?
            ");
            $stmt->execute([$vin]);
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

            // Fetch purchase info for the vehicle
            $stmt = $conn->prepare("
                SELECT *
                FROM purchase
                WHERE vin = ?
                ");
            $stmt->execute([$vin]);
            $purchase = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fetch repairs for the vehicle
            $stmt = $conn->prepare("
                SELECT r.* 
                FROM repair r
                JOIN purchase p ON r.purchase_id = p.id
                WHERE p.vin = ?
                ");
            $stmt->execute([$vin]);
            $repairs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} else {
    // Customer logic
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
}

// Calculate total repair costs
$total_repair_costs = 0;
foreach ($repairs as $repair) {
    $total_repair_costs += $repair['actual_cost'] ?? 0;
}

// Calculate profit for admin
$profit = 0;
if ($_SESSION['user_type'] === 'admin' && $sale && $purchase) {
    $profit = $sale['sale_price'] - $purchase['price_paid'] - $total_repair_costs - ($sale['commission'] ?? 0);
}
?>
<!DOCTYPE html>
<html>

<?php include('../../templates/head.php'); ?>

<body>
    <?php 
    if ($_SESSION['user_type'] === 'customer') {
        include('../../templates/customerHeader.php');
    } elseif ($_SESSION['user_type'] === 'admin') {
        include('../../templates/header.php');
    }
    ?>
    <section class="main-content" style="padding: 20px;">
        <h1>Vehicle Information</h1>
        <?php if ($_SESSION['user_type'] === 'admin' && !$vin): ?>
            <form method="GET" style="margin-bottom: 20px;">
                <label for="vin">Enter VIN:</label>
                <input type="text" name="vin" id="vin" required>
                <button type="submit">View Info</button>
            </form>
        <?php endif; ?>
        <?php if ($vehicleInfo): ?>
            <div>
                <h2><?php echo htmlspecialchars($vehicleInfo['YEAR']) . ' ' . htmlspecialchars($vehicleInfo['make']) . ' ' . htmlspecialchars($vehicleInfo['model']); ?></h2>

                <p><strong>VIN:</strong> <?php echo htmlspecialchars($vehicleInfo['vin']); ?></p>
                <p><strong>Color:</strong> <?php echo htmlspecialchars($vehicleInfo['color']); ?></p>
                <p><strong>Interior Color:</strong> <?php echo htmlspecialchars($vehicleInfo['interior_color']); ?></p>
                <p><strong>Miles:</strong> <?php echo htmlspecialchars($vehicleInfo['miles']); ?></p>
                <p><strong>Style:</strong> <?php echo htmlspecialchars($vehicleInfo['style']); ?></p>
                <p><strong>Condition:</strong> <?php echo htmlspecialchars($vehicleInfo['vehicle_condition']); ?></p><br>

                <?php if ($_SESSION['user_type'] === 'admin'): ?>
                    <p><strong>Book Price:</strong> $<?php echo htmlspecialchars(number_format($vehicleInfo['book_price'], 2)); ?></p>
                    <?php if ($purchase): ?>
                        <p><strong>Purchase Amount:</strong> $<?php echo htmlspecialchars(number_format($purchase['price_paid'], 2)); ?></p>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($sale): ?>
                    <p><strong>Sale Price:</strong> $<?php echo htmlspecialchars(number_format($sale['sale_price'], 2)); ?></p>
                    <p><strong>Amount Left to Pay:</strong> $<?php echo htmlspecialchars(number_format($sale['sale_price'] - ($sale['down_payment'] + $payments), 2)); ?></p>
                    <p><strong>Amount Paid:</strong> $<?php echo htmlspecialchars(number_format($sale['down_payment'] + $payments, 2)); ?></p>
                    
                    <?php if ($_SESSION['user_type'] === 'admin'): ?>
                        <p><strong>Employee Commission:</strong> $<?php echo htmlspecialchars(number_format($sale['commission'], 2)); ?></p>
                        <p><strong>Total Repair Costs:</strong> $<?php echo htmlspecialchars(number_format($total_repair_costs, 2)); ?></p>
                        <p ><strong>Profit:</strong> $<?php echo htmlspecialchars(number_format($profit, 2)); ?></p>
                    <?php endif; ?>
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

                <?php if ($_SESSION['user_type'] === 'admin' && !empty($repairs)): ?>
                    <h3>Repairs</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr style="border: 1px solid #ddd; background-color: #f2f2f2;">
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Description</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Estimated Cost</th>
                            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Actual Cost</th>
                        </tr>
                        <?php foreach ($repairs as $repair): ?>
                            <tr style="border: 1px solid #ddd;">
                                <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($repair['description']); ?></td>
                                <td style="border: 1px solid #ddd; padding: 8px;">$<?php echo htmlspecialchars(number_format($repair['estimated_cost'], 2)); ?></td>
                                <td style="border: 1px solid #ddd; padding: 8px;">$<?php echo htmlspecialchars(number_format($repair['actual_cost'], 2)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>No vehicle information found.</p>
        <?php endif; ?>
    </section>

</body>

</html>
