<?php

require '../config/db.php';
// get number of cars bought last month
$stmt = $conn->prepare("
    SELECT COUNT(v.vin)
    FROM vehicle AS v
    NATURAL JOIN purchase AS p
    WHERE YEAR(p.purchase_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) 
    AND MONTH(p.purchase_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
");
$stmt->execute();
$purchaseCount = $stmt->fetchColumn();

// Display cars that got bought
$stmt = $conn->prepare("
    SELECT v.year, v.make, v.model, p.price_paid
    FROM vehicle AS v
    NATURAL JOIN purchase AS p
    WHERE YEAR(p.purchase_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) 
    AND MONTH(p.purchase_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
");
$stmt->execute();
$purchased = $stmt->fetchAll(PDO::FETCH_ASSOC);

// get number of cars sold last month
$stmt = $conn->prepare("
    SELECT COUNT(v.vin)
    FROM vehicle AS v
    NATURAL JOIN sale AS s
    WHERE YEAR(s.sale_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) 
    AND MONTH(s.sale_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
");
$stmt->execute();
$saleCount = $stmt->fetchColumn();

// Display cars that got sold
$stmt = $conn->prepare("
    SELECT v.year, v.make, v.model, p.price_paid, s.sale_price, s.sale_price - p.price_paid AS profit
    FROM vehicle AS v
    NATURAL JOIN sale AS s
    NATURAL JOIN purchase AS p
    WHERE YEAR(s.sale_date) = YEAR(CURDATE() - INTERVAL 1 MONTH) 
    AND MONTH(s.sale_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
");
$stmt->execute();
$sold = $stmt->fetchAll(PDO::FETCH_ASSOC);

// get number of payments made by customers last month
$stmt = $conn->prepare("
    SELECT COUNT(id)
    FROM payment
    WHERE YEAR(paid_date) = YEAR(CURDATE() - INTERVAL 1 MONTH)
    AND MONTH(paid_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
");
$stmt->execute();
$paymentCount = $stmt->fetchColumn();

// get total amount paid from customers last month
$stmt = $conn->prepare("
    SELECT SUM(amount)
    FROM payment
    WHERE YEAR(paid_date) = YEAR(CURDATE() - INTERVAL 1 MONTH)
    AND MONTH(paid_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
");
$stmt->execute();
$paymentSum = $stmt->fetchColumn();

// get number of late payments
$stmt = $conn->prepare("
    SELECT COUNT(id)
    FROM payment
    WHERE YEAR(paid_date) = YEAR(CURDATE() - INTERVAL 1 MONTH)
    AND MONTH(paid_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
    AND paid_date > payment_date
");
$stmt->execute();
$latePayments = $stmt->fetchColumn();

// Display late payment customers
$stmt = $conn->prepare("
    SELECT c.lastName, c.firstName, c.phone
    FROM payment AS p
    NATURAL JOIN customer AS c
    WHERE YEAR(p.paid_date) = YEAR(CURDATE() - INTERVAL 1 MONTH)
    AND MONTH(p.paid_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
    AND p.paid_date > p.payment_date
");
$stmt->execute();
$latePayers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>

<?php include('../templates/head.php'); ?>

<body>
    <?php include('../templates/header.php'); ?>
    <section class="main-content">
        <h1>Report for <?php echo date('F Y', strtotime('-1 month')); ?></h1>
        <h2>Purchased Cars:</h2>
        <p>Number of Cars Purchased: <?php echo $purchaseCount; ?></p>
        <table border="1">
            <tr>
                <th>Year</th>
                <th>Make</th>
                <th>Model</th>
                <th>Price Paid</th>
            </tr>
            <?php foreach ($purchased as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['year']); ?></td>
                    <td><?php echo htmlspecialchars($row['make']); ?></td>
                    <td><?php echo htmlspecialchars($row['model']); ?></td>
                    <td><?php echo htmlspecialchars($row['price_paid']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table><br><br>

        <h2>Sold Cars:</h2>
        <p>Number of Cars Sold: <?php echo $saleCount; ?></p>
        <table border="1">
            <tr>
                <th>Year</th>
                <th>Make</th>
                <th>Model</th>
                <th>Price Paid</th>
                <th>Sale Price</th>
                <th>Profit</th>
            </tr>
            <?php foreach ($sold as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['year']); ?></td>
                    <td><?php echo htmlspecialchars($row['make']); ?></td>
                    <td><?php echo htmlspecialchars($row['model']); ?></td>
                    <td><?php echo htmlspecialchars($row['price_paid']); ?></td>
                    <td><?php echo htmlspecialchars($row['sale_price']); ?></td>
                    <td><?php echo htmlspecialchars($row['profit']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table><br><br>
        <h2>Payments:</h2>
        <p>Number of Payments Made: <?php echo $paymentCount; ?></p>
        <p>Total Amount Payed: <?php echo $paymentSum; ?></p>
        <p>Number of Late Payments: <?php echo $latePayments; ?></p>
        <table border="1">
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
            </tr>
            <?php foreach ($latePayers as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['firstName']); ?></td>
                    <td><?php echo htmlspecialchars($row['lastName']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>

</body>

</html>
