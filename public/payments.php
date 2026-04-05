<?php

require '../config/db.php';


class Payment
{
    private int $customer_id;
    private int $sale_id;
    private string $payment_date;
    private string $paid_date;
    private float $due;
    private float $amount;
    private int $bank_id;

    public function __construct(array $_post)
    {
        $this->customer_id = (int) ($_post['customer_id'] ?? 0);
        $this->sale_id = (int) ($_post['sale_id'] ?? 0);
        $this->payment_date = $_post['payment_date'] ?? '';
        $this->paid_date = $_post['paid_date'];
        $this->due = (float) ($_post['due'] ?? 0);
        $this->amount = (float) ($_post['amount'] ?? 0);
        $this->bank_id = (int) ($_post['bank_id'] ?? 0);
    }

    public function save($conn): void
    {
        $stmt = $conn->prepare("
      INSERT INTO payment (customer_id, sale_id, payment_date, due, paid_date, amount, bank_id) 
      VALUES (:customer_id, :sale_id, :payment_date, :due, :paid_date, :amount, :bank_id)
    ");
        $stmt->execute([
            ":customer_id" => $this->customer_id,
            ":sale_id" => $this->sale_id,
            ":payment_date" => $this->payment_date,
            ":paid_date" => $this->paid_date,
            ":due" => $this->due,
            ":amount" => $this->amount,
            ":bank_id" => $this->bank_id
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment = new Payment($_POST);
    try {
        $conn->beginTransaction();
        $payment->save($conn);
        $conn->commit();
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        $error = "Database error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html>

<?php include('../templates/head.php'); ?>

<body>
    <?php include('../templates/header.php'); ?>
    <section class="main-content">
        <form method="POST">
            <h2>Payment Details</h2>
            <label for="customer_id">Customer ID:</label>
            <input type="number" id="customer_id" name="customer_id" min="0" step="1" required><br><br>

            <label for="sale_id">Sale ID:</label>
            <input type="number" id="sale_id" name="sale_id" min="0" step="1" required><br><br>

            <label for="payment_date">Payment Date</label>
            <input type="date" id="payment_date" name="payment_date" required><br><br>

            <label for="paid_date">Paid Date</label>
            <input type="date" id="paid_date" name="paid_date" required><br><br>

            <label for="due">Due</label>
            <input type="number" id="due" name="due" min="0.01" step="0.01" required><br><br>

            <label for="amount">Amount</label>
            <input type="number" id="amount" name="amount" min="0" step="0.01" required><br><br>

            <label for="bank_id">Bank ID</label>
            <input type="number" id="bank_id" name="bank_id" min="0" step="1" required><br><br>

            <button type="submit">Submit</button>
        </form>
    </section>

</body>

</html>
