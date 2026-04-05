<?php
session_start();
if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'customer' && $_SESSION['user_type'] !== 'admin')) {
    header('Location: index.php');
    exit;
}

require '../../config/db.php';

$user_type = $_SESSION['user_type'];
$vin = $_GET['vin'] ?? null;
$payments = [];
$amount_due = null;
$next_payment = null;
$late_payment_count = 0;
$average_days_late = 0;

if ($user_type === 'customer') {
    if ($vin) {
        $customer_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("
            SELECT id 
            FROM sale 
            WHERE vin = ? AND customer_id = ?
            ");
        $stmt->execute([$vin, $customer_id]);
        $sale = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sale) {
            $stmt = $conn->prepare("
                SELECT * 
                FROM payment 
                WHERE sale_id = ? 
                ORDER BY paid_date DESC
                ");
            $stmt->execute([$sale['id']]);
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($payments) {
                $amount_due = $payments[0]['amount'];
                $last_due_dates = array_column($payments, 'payment_date');
                $last_due = max($last_due_dates);
                $next_payment = date('Y-m-d', strtotime($last_due . ' +1 month'));
            }
        }
    }
} elseif ($user_type === 'admin') {
    if ($vin) {
        $stmt = $conn->prepare("
            SELECT p.* 
            FROM payment p
            JOIN sale s ON p.sale_id = s.id
            WHERE s.vin = ?
            ORDER BY p.paid_date DESC
            ");
        $stmt->execute([$vin]);
    } else {
        $stmt = $conn->prepare("
            SELECT p.* 
            FROM payment p
            ORDER BY p.paid_date DESC
            ");
        $stmt->execute();
    }
    
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($payments)) {
        $late_payments = [];
        foreach ($payments as $payment) {
            if (!empty($payment['paid_date']) && $payment['paid_date'] > $payment['payment_date']) {
                $paid_date = new DateTime($payment['paid_date']);
                $due_date = new DateTime($payment['payment_date']);
                $days_late = $paid_date->diff($due_date)->days;
                $late_payments[] = $days_late;
            }
        }
        
        $late_payment_count = count($late_payments);
        if ($late_payment_count > 0) {
            $average_days_late = round(array_sum($late_payments) / $late_payment_count, 2);
        }
    }
}
?>
<!DOCTYPE html>
<html>

<?php include('../../templates/head.php'); ?>

<body>
    <?php 
    if ($user_type === 'admin') {
        include('../../templates/header.php');
    } else {
        include('../../templates/customerHeader.php');
    }
    ?>
    <section class="main-content">
        <h1>Payment History</h1>
        
        <?php if ($user_type === 'admin'): ?>
            <div class="filter-section">
                <h3>Filter Payments</h3>
                <form method="GET">
                    <label for="vin-input">Filter by Vehicle VIN:</label>
                    <input type="text" id="vin-input" name="vin" value="<?php echo htmlspecialchars($vin ?? ''); ?>">
                    <button type="submit" >Filter</button>
                    <button href="paymentHistoryView.php">Clear Filter</button>
                </form>
            </div>
            
            <?php if (!empty($payments)): ?>
                <div class="statistics-section">
                    <h3>Payment Statistics</h3>
                    <p><strong>Total Payments:</strong> <?php echo count($payments); ?></p>
                    <p><strong>Late Payments:</strong> <?php echo $late_payment_count; ?></p>
                    <p><strong>Average Days Late:</strong> <?php echo $average_days_late > 0 ? $average_days_late . ' days' : 'No late payments'; ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($user_type === 'customer' && isset($next_payment)): ?>
            <p><strong>Next Payment:</strong> <?php echo htmlspecialchars($next_payment); ?> - <strong>Amount Due:</strong> $<?php echo htmlspecialchars(number_format($amount_due, 2)); ?></p>
        <?php endif; ?>
        
        <?php if ($payments): ?>
            <table>
                <thead>
                    <tr>
                        <?php if ($user_type === 'admin'): ?>
                            <th>Customer ID</th>
                            <th>Vehicle VIN</th>
                        <?php endif; ?>
                        <th>Payment Due Date</th>
                        <th>Amount Due</th>
                        <th>Paid Date</th>
                        <th>Amount Paid</th>
                        <?php if ($user_type === 'admin'): ?>
                            <th>Days Late</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): 
                        $days_late = '';
                        if ($user_type === 'admin' && !empty($payment['paid_date']) && $payment['paid_date'] > $payment['payment_date']) {
                            $paid_date = new DateTime($payment['paid_date']);
                            $due_date = new DateTime($payment['payment_date']);
                            $days_late = $paid_date->diff($due_date)->days;
                        }
                    ?>
                        <tr>
                            <?php if ($user_type === 'admin'): ?>
                                <td><?php echo htmlspecialchars($payment['customer_id']); ?></td>
                                <td>
                                    <?php 
                                    $stmt = $conn->prepare("SELECT vin FROM sale WHERE id = ?");
                                    $stmt->execute([$payment['sale_id']]);
                                    $sale = $stmt->fetch(PDO::FETCH_ASSOC);
                                    echo htmlspecialchars($sale['vin'] ?? '');
                                    ?>
                                </td>
                            <?php endif; ?>
                            <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($payment['due'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($payment['paid_date']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($payment['amount'], 2)); ?></td>
                            <?php if ($user_type === 'admin'): ?>
                                <td>
                                    <?php echo $days_late ? $days_late . ' days' : '-'; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No payment history found<?php echo $vin ? ' for VIN ' . htmlspecialchars($vin) : ''; ?>.</p>
        <?php endif; ?>
    </section>

</body>

</html>
