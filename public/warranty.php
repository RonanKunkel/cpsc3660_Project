<?php
require '../config/db.php';

$sale_id = $employee_id = $customer_id = '';
$success = false;
$error = '';

// warranty display stuff
$selected_warranty_type =
    $_POST['warranties'][0]['warranty_type_id'] ?? '';

$items_covered_display = '';

if (!empty($selected_warranty_type)) {
    $stmt = $conn->prepare("
        SELECT items_covered
        FROM warranty_types
        WHERE id = :id
    ");
    $stmt->execute([':id' => $selected_warranty_type]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $items_covered_display = $row['items_covered'];
    }
}

// form stuff
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_form'])) {

    $employee_id = trim($_POST['employee_id'] ?? '');
    $customer_id = trim($_POST['customer_id'] ?? '');

    $warranty = [
        'start_date' => trim($_POST['warranties'][0]['start_date'] ?? ''),
        'end_date' => trim($_POST['warranties'][0]['end_date'] ?? ''),
        'warranty_type_id' => (int)($_POST['warranties'][0]['warranty_type_id'] ?? 0),
        'cost' => (float)($_POST['warranties'][0]['cost'] ?? 0),
        'monthly_cost' => (float)($_POST['warranties'][0]['monthly_cost'] ?? 0),
        'deductible' => (float)($_POST['warranties'][0]['deductible'] ?? 0)
    ];

    // error stuff if missing input
    if (empty($employee_id)) {
        $error = "Salesperson ID required.";
    } elseif (empty($customer_id)) {
        $error = "Customer ID required.";
    } else {
        try {
            $conn->beginTransaction();

            // create sale
            $stmt = $conn->prepare("
                INSERT INTO sale (vin, customer_id, employee_id, sale_date, sale_price)
                VALUES (:vin, :customer_id, :employee_id, :sale_date, :sale_price)
            ");

            $stmt->execute([
                ':vin' => $_POST['vin'] ?? '',
                ':customer_id' => $customer_id,
                ':employee_id' => $employee_id,
                ':sale_date' => $_POST['sale_date'] ?? date('Y-m-d'),
                ':sale_price' => (float)($_POST['sale_price'] ?? 0)
            ]);

            $sale_id = $conn->lastInsertId();

            //get warranty type info
            $typeStmt = $conn->prepare("
                SELECT name, items_covered
                FROM warranty_types
                WHERE id = :id
            ");

            $typeStmt->execute([
                ':id' => $warranty['warranty_type_id']
            ]);

            $type = $typeStmt->fetch(PDO::FETCH_ASSOC);

            // insert the warranty
            $stmt = $conn->prepare("
                INSERT INTO warranty (sale_id, start_date, end_date, policy_name, items_covered, cost, monthly_cost, deductible)
                VALUES (:sale_id, :start_date, :end_date, :policy_name, :items_covered, :cost, :monthly_cost, :deductible)
            ");

            $stmt->execute([
                ':sale_id' => $sale_id,
                ':start_date' => $warranty['start_date'],
                ':end_date' => $warranty['end_date'],
                ':policy_name' => $type['name'],
                ':items_covered' => $type['items_covered'],
                ':cost' => $warranty['cost'],
                ':monthly_cost' => $warranty['monthly_cost'],
                ':deductible' => $warranty['deductible']
            ]);

            $conn->commit();
            $success = true;

        } catch (PDOException $e) {
            $conn->rollBack();
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>

<?php include('../templates/head.php'); ?>

<body>
<?php include('../templates/header.php'); ?>

<section class="main-content">

<h2>Enter Warranty for Vehicle Sale</h2>

<?php if ($success): ?>
    <p style="color:green;">Warranty saved successfully!</p>
<?php elseif ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">

<h3>Vehicle Info</h3>

<label>VIN:</label>
<input type="text" name="vin" maxlength="17" required><br><br>

<label>Sale Date:</label>
<input type="date" name="sale_date" required><br><br>

<label>Sale Price:</label>
<input type="number" step="0.01" name="sale_price" required><br><br>

<h3>Employee</h3>
<input type="number" name="employee_id" required><br><br>

<h3>Customer</h3>
<input type="number" name="customer_id" required><br><br>

<h3>Warranty</h3>

<label>Start Date:</label>
<input type="date" name="warranties[0][start_date]" required><br>

<label>End Date:</label>
<input type="date" name="warranties[0][end_date]" required><br>

<label>Warranty Type:</label>
<select name="warranties[0][warranty_type_id]"
        onchange="this.form.submit()" required>

<?php
$types = $conn->query("SELECT id, name FROM warranty_types");
foreach ($types as $t) {
    $selected = ($selected_warranty_type == $t['id']) ? 'selected' : '';
    echo "<option value='{$t['id']}' $selected>{$t['name']}</option>";
}
?>
</select><br>

<?php if (!empty($items_covered_display)): ?>
<p style="font-style:italic; color:#555;">
    Items Covered: <?= htmlspecialchars($items_covered_display) ?>
</p>
<?php endif; ?>

<br>

<label>Cost:</label>
<input type="number" step="0.01" name="warranties[0][cost]" required><br>

<label>Monthly Cost:</label>
<input type="number" step="0.01" name="warranties[0][monthly_cost]" required><br>

<label>Deductible:</label>
<input type="number" step="0.01" name="warranties[0][deductible]" required><br><br>

<button type="submit" name="submit_form">Submit</button>

</form>

</section>
</body>
</html>
