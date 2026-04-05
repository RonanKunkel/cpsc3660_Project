<?php
require '../../config/db.php';

// Set filter defaults
$make = isset($_GET['make']) && $_GET['make'] !== '' ? (string)$_GET['make'] : '%';
$model = isset($_GET['model']) && $_GET['model'] !== '' ? (string)$_GET['model'] : '%';
$yearMin = isset($_GET['year_min']) && $_GET['year_min'] !== '' ? (int)$_GET['year_min'] : 1900;
$yearMax = isset($_GET['year_max']) && $_GET['year_max'] !== '' ? (int)$_GET['year_max'] : 2100;
$kmMin = isset($_GET['km_min']) && $_GET['km_min'] !== '' ? (int)$_GET['km_min'] : 0;
$kmMax = isset($_GET['km_max']) && $_GET['km_max'] !== '' ? (int)$_GET['km_max'] : 10000000;
$priceMin = isset($_GET['price_min']) && $_GET['price_min'] !== '' ? (float)$_GET['price_min'] : 0;
$priceMax = isset($_GET['price_max']) && $_GET['price_max'] !== '' ? (float)$_GET['price_max'] : 10000000;

// get number of cars on lot
$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM vehicle AS v
    NATURAL JOIN purchase AS p
    WHERE v.vin NOT IN (SELECT vin FROM sale)
      AND v.year BETWEEN :yearMin AND :yearMax
      AND v.miles BETWEEN :kmMin AND :kmMax
      AND v.book_price BETWEEN :priceMin AND :priceMax
");
$stmt->execute([
    ':yearMin' => $yearMin,
    ':yearMax' => $yearMax,
    ':kmMin' => $kmMin,
    ':kmMax' => $kmMax,
    ':priceMin' => $priceMin,
    ':priceMax' => $priceMax,
]);
$carAmount = $stmt->fetchColumn();

// Display cars on lot
$stmt = $conn->prepare("
    (SELECT v.year, v.make, v.model, v.miles, v.book_price, p.price_paid, p.purchase_date, DATEDIFF(CURDATE(), p.purchase_date) AS days_on_lot
    FROM vehicle AS v
    NATURAL JOIN purchase AS p
    WHERE v.vin NOT IN (SELECT vin FROM sale)
      AND v.year BETWEEN :yearMin AND :yearMax
      AND v.miles BETWEEN :kmMin AND :kmMax
      AND v.book_price BETWEEN :priceMin AND :priceMax
      AND v.make LIKE :make AND v.model LIKE :model)
");
$stmt->execute([
    ':make' => $make,
    ':model' => $model,
    ':yearMin' => $yearMin,
    ':yearMax' => $yearMax,
    ':kmMin' => $kmMin,
    ':kmMax' => $kmMax,
    ':priceMin' => $priceMin,
    ':priceMax' => $priceMax,
]);
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>

<?php include('../../templates/head.php'); ?>

<body>
    <?php include('../../templates/header.php'); ?>
    <section class="main-content">
        <div>
            <h2>Inventory:</h2>
        </div>
        <div>
            <form method="GET" action="">
                <table>
                    <tr>
                        <td><strong>Car:</strong></td>
                        <td>Make: <input type="text" name="make" value="<?php echo isset($_GET['make']) ? htmlspecialchars($_GET['make']) : ''; ?>" placeholder="Ford"></td>
                        <td>Model: <input type="text" name="model" value="<?php echo isset($_GET['model']) ? htmlspecialchars($_GET['model']) : ''; ?>" placeholder="Mustang"></td>
                    </tr>
                    <tr>
                        <td><strong>Year:</strong></td>
                        <td>Min: <input type="text" name="year_min" value="<?php echo isset($_GET['year_min']) ? htmlspecialchars($_GET['year_min']) : ''; ?>" placeholder="1900"></td>
                        <td>Max: <input type="text" name="year_max" value="<?php echo isset($_GET['year_max']) ? htmlspecialchars($_GET['year_max']) : ''; ?>" placeholder="2100"></td>
                    </tr>
                    <tr>
                        <td><strong>KM's:</strong></td>
                        <td>Min: <input type="text" name="km_min" value="<?php echo isset($_GET['km_min']) ? htmlspecialchars($_GET['km_min']) : ''; ?>" placeholder="0"></td>
                        <td>Max: <input type="text" name="km_max" value="<?php echo isset($_GET['km_max']) ? htmlspecialchars($_GET['km_max']) : ''; ?>" placeholder="10000000"></td>
                    </tr>
                    <tr>
                        <td><strong>Book Price:</strong></td>
                        <td>Min: <input type="text" name="price_min" value="<?php echo isset($_GET['price_min']) ? htmlspecialchars($_GET['price_min']) : ''; ?>" placeholder="0"></td>
                        <td>Max: <input type="text" name="price_max" value="<?php echo isset($_GET['price_max']) ? htmlspecialchars($_GET['price_max']) : ''; ?>" placeholder="10000000"></td>
                    </tr>
                </table>
                <br>
                <button type="submit">Filter</button>
                <a href="?"><button type="button">Reset</button></a>
            </form>
        </div>
        <br>
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
