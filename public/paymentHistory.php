<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: index.php');
    exit;
}

require '../config/db.php';

$vin = $_GET['vin'] ?? null;
$payments = [];

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
?>
<!DOCTYPE html>
<html>

<?php include('../templates/head.php'); ?>

<body>
    <?php include('../templates/customerHeader.php'); ?>
    <section class="main-content" style="padding: 20px;">
        <h1>Payment History</h1>
        <?php if (isset($next_payment)): ?>
            <p><strong>Next Payment:</strong> <?php echo htmlspecialchars($next_payment); ?> - <strong>Amount Due:</strong> $<?php echo htmlspecialchars(number_format($amount_due, 2)); ?></p>
        <?php endif; ?>
        <?php if ($payments): ?>
            <table style="border-collapse: collapse; width: 100%; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="border: 1px solid #ddd; padding: 8px;">Payment Due Date</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Amount Due</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Paid Date</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                            <td style="border: 1px solid #ddd; padding: 8px;">$<?php echo htmlspecialchars(number_format($payment['amount'], 2)); ?></td>
                            <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($payment['paid_date']); ?></td>
                            <td style="border: 1px solid #ddd; padding: 8px;">$<?php echo htmlspecialchars(number_format($payment['amount'], 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No payment history found for this vehicle.</p>
        <?php endif; ?>
    </section>

</body>

</html>
