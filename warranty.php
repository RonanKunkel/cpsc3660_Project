<?php
require 'db.php';

$sale_id = $employee_id = $customer_id = '';
$warranties = [];
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = trim($_POST['employee_id'] ?? '');
    $customer_id = trim($_POST['customer_id'] ?? '');

    // Warranties
    if (!empty($_POST['warranties'])) {
        foreach ($_POST['warranties'] as $w) {
            $warranties[] = [
                'start_date'    => trim($w['start_date'] ?? ''),
                'end_date'      => trim($w['end_date'] ?? ''),
                'warranty_type_id' => (int)($w['warranty_type_id'] ?? 0),
                'cost'          => (float)($w['cost'] ?? 0),
                'monthly_cost'  => (float)($w['monthly_cost'] ?? 0),
                'deductible'    => (float)($w['deductible'] ?? 0)
            ];
        }
    }

    // Validation
    if (empty($employee_id)) $error = "Salesperson ID required.";
    elseif (empty($customer_id)) $error = "Customer ID required.";
    elseif (empty($warranties)) $error = "At least one warranty must be added.";
    else {
        try {
            $conn->beginTransaction();

            // Create a sale record (assuming one vehicle per form)
            $stmt = $conn->prepare("
                INSERT INTO sale (vin, customer_id, employee_id, sale_date, sale_price)
                VALUES (:vin, :customer_id, :employee_id, :sale_date, :sale_price)
            ");
            $stmt->execute([
                ':vin'          => $_POST['vin'] ?? '',
                ':customer_id'  => $customer_id,
                ':employee_id'  => $employee_id,
                ':sale_date'    => $_POST['sale_date'] ?? date('Y-m-d'),
                ':sale_price'   => (float)($_POST['sale_price'] ?? 0)
            ]);

            $sale_id = $conn->lastInsertId();

            // Insert warranties
            $stmt = $conn->prepare("
                INSERT INTO warranty (sale_id, start_date, end_date, policy_name, items_covered, cost, monthly_cost, deductible)
                VALUES (:sale_id, :start_date, :end_date, :policy_name, :items_covered, :cost, :monthly_cost, :deductible)
            ");

            foreach ($warranties as $w) {
                $typeStmt = $conn->prepare("
                    SELECT name, items_covered
                    FROM warranty_types
                    WHERE id = :id
                ");
                $typeStmt->execute([
                    ':id' => $w['warranty_type_id']
                ]);
                $type = $typeStmt->fetch(PDO::FETCH_ASSOC);

                $stmt->execute([
                    ':sale_id'       => $sale_id,
                    ':start_date'    => $w['start_date'],
                    ':end_date'      => $w['end_date'],
                    ':policy_name'   => $type['name'],
                    ':items_covered' => $type['items_covered'],
                    ':cost'          => $w['cost'],
                    ':monthly_cost'  => $w['monthly_cost'],
                    ':deductible'    => $w['deductible']
                ]);
            }

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
<head>
    <title>Enter Warranties</title>
</head>
<body>
<h1>Enter Warranties for Vehicle Sale</h1>

<?php if ($success): ?>
    <p style="color:green;">Warranties saved successfully!</p>
<?php elseif ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <h2>Vehicle Info</h2>
    <label for="vin">VIN:</label>
    <input type="text" id="vin" name="vin" maxlength="17" required><br><br>

    <label for="sale_date">Sale Date:</label>
    <input type="date" id="sale_date" name="sale_date" required><br><br>

    <label for="sale_price">Sale Price:</label>
    <input type="number" step="0.01" id="sale_price" name="sale_price" required><br><br>

    <h2>Employee (Salesperson)</h2>
    <label for="employee_id">Employee ID:</label>
    <input type="number" id="employee_id" name="employee_id" required><br><br>

    <h2>Customer</h2>
    <label for="customer_id">Customer ID:</label>
    <input type="number" id="customer_id" name="customer_id" required><br><br>

    <h2>Warranties</h2>
    <div id="warranties-container"></div>
    <button type="button" id="add-warranty-btn">Add Warranty</button><br><br>

    <button type="submit">Submit</button>
</form>

<script>
let warrantyCount = 0;
const container = document.getElementById('warranties-container');

document.getElementById('add-warranty-btn').addEventListener('click', () => {
    const index = warrantyCount++;
    const div = document.createElement('div');
    div.innerHTML = `
        <hr>
        <button type="button" onclick="this.parentElement.remove()">Remove</button><br>
        <label>Start Date:</label>
        <input type="date" name="warranties[${index}][start_date]" required><br>
        <label>End Date:</label>
        <input type="date" name="warranties[${index}][end_date]" required><br>
        <label>Warranty Type:</label>
        <select name="warranties[${index}][warranty_type_id]" required>
            <?php
            $types = $conn->query("SELECT id, name FROM warranty_types");
            foreach ($types as $t) {
                echo "<option value='{$t['id']}'>{$t['name']}</option>";
            }
            ?>
        </select><br>
        <label>Cost:</label>
        <input type="number" step="0.01" name="warranties[${index}][cost]" required><br>
        <label>Monthly Cost:</label>
        <input type="number" step="0.01" name="warranties[${index}][monthly_cost]" required><br>
        <label>Deductible:</label>
        <input type="number" step="0.01" name="warranties[${index}][deductible]" required><br>
    `;
    container.appendChild(div);
});
</script>
</body>
</html>
