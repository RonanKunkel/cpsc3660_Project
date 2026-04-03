<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'customer') {
    header('Location: index.php');
    exit;
}

require '../config/db.php';

$vin = $_GET['vin'] ?? null;
$sale = null;
$last_payment = null;
$next_due = null;
$due_amount = null;

if ($vin) {
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
            SELECT * 
            FROM payment 
            WHERE sale_id = ? 
            ORDER BY payment_date DESC LIMIT 1");
        $stmt->execute([$sale['id']]);
        $last_payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($last_payment) {
            $next_due = date('Y-m-d', strtotime($last_payment['payment_date'] . ' +1 month'));
            $due_amount = $last_payment['amount'];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = (float)$_POST['amount'];
    $bank_id = $_POST['bank_id'];
    if ($sale && $next_due && $due_amount && $bank_id) {
        $stmt = $conn->prepare("
            INSERT INTO payment (sale_id, payment_date, paid_date, amount, customer_id, due, bank_id) VALUES 
                (?, ?, ?, ?, ?, ?, ?)
                ");
        $stmt->execute([$sale['id'], $next_due, date('Y-m-d'), $amount, $customer_id, $due_amount, $bank_id]);
        $success = "Payment of $" . number_format($amount, 2) . " made successfully.";
    }
}
?>
<!DOCTYPE html>
<html>

<?php include('../templates/head.php'); ?>

<body>
    <?php include('../templates/customerHeader.php'); ?>
    <section class="main-content" style="padding: 20px;">
        <h1>Make Payment</h1>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <?php if ($sale && $next_due): ?>
            <form method="post">
                <p><strong>Next Due Date:</strong> <?php echo htmlspecialchars($next_due); ?></p>
                <p><strong>Due Amount:</strong> $<?php echo htmlspecialchars(number_format($due_amount, 2)); ?></p>
                <label for="amount">Amount to Pay:</label>
                <input type="number" step="0.01" name="amount" id="amount" value="<?php echo htmlspecialchars($due_amount); ?>" required>
                <br><br>
                <label for="bank_id">Bank ID:</label>
                <input type="text" name="bank_id" id="bank_id" required>
                <br><br>
                <button type="submit">Submit Payment</button>
            </form>
        <?php else: ?>
            <p>No payment information available.</p>
        <?php endif; ?>
    </section>

</body>

</html>