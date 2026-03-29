<?php
require 'db.php';

// sale
$sale_date = $sale_price = $down_payment = $financed_amount = $commission  = '';
// salesperson
$employee_id = '';
// customer
$customer_id = '';
// vehicle
$vin = '';
$success = false;
$error = '';

if (isset($_GET['success'])) {
    $success = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // sale
    $sale_date = trim($_POST['sale_date'] ?? '');
    $sale_price = (float)($_POST['sale_price'] ?? 0);
    $down_payment = (float)($_POST['down_payment'] ?? 0);
    $financed_amount = (float)($_POST['financed_amount'] ?? 0);
    $commission = (float)($_POST['commission'] ?? 0);

    // saleperson
    $employee_id = (int)($_POST['employee_id'] ?? 0);

    // customer
    $customer_id = (int)($_POST['customer_id']?? 0);
    
    // vehicle
    $vin = trim($_POST['vin'] ?? '');


    if ($sale_date === '' || $sale_price <= 0 || $down_payment < 0 || $financed_amount < 0 || $commission < 0) {
        $error = "You must fill every field.";
    } elseif (empty($employee_id) || empty($customer_id)) {
        $error = "Both Id's are required.";
    } elseif (empty($vin) || strlen($vin) !== 17) {
        $error = "VIN must be exactly 17 characters.";
    } else {
        try {
            $conn->beginTransaction();

            // Add new sale
            $stmt = $conn->prepare("
                INSERT INTO sale (customer_id, vin, employee_id, sale_date, sale_price, down_payment, financed_amount, commission) 
                VALUES (:customer_id, :vin, :employee_id, :sale_date, :sale_price, :down_payment, :financed_amount, :commission) 
            ");
            $stmt->execute([
                ':customer_id' => $customer_id,
                ':vin' => $vin,
                ':employee_id' => $employee_id,
                ':sale_date' => $sale_date,
                ':sale_price' => $sale_price,
                ':down_payment' => $down_payment,
                ':financed_amount' => $financed_amount,
                ':commission'            => $commission,
            ]);

            $conn->commit();

            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit;

        } catch (PDOException $e) {
            $conn->rollBack();
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <?php include('templates/header.php'); ?>
<body>
    <h1>Enter Car Selling Details</h1>

    <?php if ($success): ?>
        <p class="success">Vehicle sold and saved successfully!</p>
    <?php elseif ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <h2>Identifiers</h2>

        <label for="customer_id">Customer ID:</label>
        <input type="number" id="customer_id" name="customer_id" min="0" required><br><br>

        <label for="vin">VIN:</label>
        <input type="text" id="vin" name="vin" maxlength="17" minlength="17" required><br><br>

        <label for="employee_id">Employee ID:</label>
        <input type="number" id="employee_id" name="employee_id" min="0" required><br><br>

         <h2>Sale Info</h2>

        <label for="sale_date">Sale Date:</label>
        <input type="date" id="sale_date" name="sale_date" required><br><br>

        <label for="sale_price">Sale Price:</label>
        <input type="number" id="sale_price" name="sale_price" min="0" required><br><br>

        <label for="down_payment">Down Payment:</label>
        <input type="number" id="down_payment" name="down_payment" min="0" required><br><br>

        <label for="financed_amount">Financed Amount:</label>
        <input type="number" id="financed_amount" name="financed_amount" min="0" required><br><br>

        <label for="commission">Commission:</label>
        <input type="number" id="commission" name="commission" min="0" required><br><br>

        <button type="submit">Submit</button>
    </form>

</body>
</html>
