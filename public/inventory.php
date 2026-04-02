<?php
require '../config/db.php';

// get number of cars on lot
$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM vehicle
    WHERE vin NOT IN (SELECT vin
                        FROM sale)
");
$stmt->execute();
$carAmount = $stmt->fetchColumn();

// Display cars on lot
$stmt = $conn->prepare("
    SELECT v.year, v.make, v.model, v.miles, v.book_price, p.price_paid, p.purchase_date, DATEDIFF(CURDATE(), p.purchase_date) AS days_on_lot
    FROM vehicle AS v
    NATURAL JOIN purchase AS p
    WHERE v.vin NOT IN (SELECT vin
                        FROM sale)
");
$stmt->execute();
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>

<?php include('../templates/head.php'); ?>

<body>
    <?php include('../templates/header.php'); ?>
    <section class="main-content">
        <div>
            <h2>Inventory:</h2>
        </div>
        <div>
            <p>Cars on Lot: <?php echo $carAmount; ?></p>
            <table border="1">
                <tr>
                    <th>Year</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>KM's</th>
                    <th>Book Price</th>
                    <th>Price Paid</th>
                    <th>Purchase Date</th>
                    <th>Days on Lot</th>
                </tr>
                <?php foreach ($inventory as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['year']); ?></td>
                        <td><?php echo htmlspecialchars($row['make']); ?></td>
                        <td><?php echo htmlspecialchars($row['model']); ?></td>
                        <td><?php echo htmlspecialchars($row['miles']); ?></td>
                        <td><?php echo htmlspecialchars($row['book_price']); ?></td>
                        <td><?php echo htmlspecialchars($row['price_paid']); ?></td>
                        <td><?php echo htmlspecialchars($row['purchase_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['days_on_lot']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </section>
</body>

</html>
