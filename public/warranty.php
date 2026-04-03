<?php
require '../config/db.php';

$sale_id = '';
$success = false;
$error = '';

// form processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_form'])) {

    $sale_id = trim($_POST['sale_id'] ?? '');

    $warranty = [
        'start_date' => trim($_POST['warranties'][0]['start_date'] ?? ''),
        'end_date' => trim($_POST['warranties'][0]['end_date'] ?? ''),
        'warranty_type_id' => (int)($_POST['warranties'][0]['warranty_type_id'] ?? 0),
        'cost' => (float)($_POST['warranties'][0]['cost'] ?? 0),
        'monthly_cost' => (float)($_POST['warranties'][0]['monthly_cost'] ?? 0),
        'deductible' => (float)($_POST['warranties'][0]['deductible'] ?? 0)
    ];

    // error handling
    if (empty($sale_id)) {
        $error = "Sale ID required.";
    } else {
        try {
            $conn->beginTransaction();

            // get warranty type info
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
<label>Sale ID:</label>
<input type="number" name="sale_id" required><br><br>

<h3>Warranty</h3>

<label>Start Date:</label>
<input type="date" name="warranties[0][start_date]" required><br>

<label>End Date:</label>
<input type="date" name="warranties[0][end_date]" required><br>

<label>Warranty Type:</label>
<select id="warranty_type" name="warranties[0][warranty_type_id]" required>
    <option value="">Select Warranty</option>
    <?php
    $types = $conn->query("SELECT id, name FROM warranty_types");
    foreach ($types as $t) {
        echo "<option value='{$t['id']}'>{$t['name']}</option>";
    }
    ?>
</select><br><br>

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