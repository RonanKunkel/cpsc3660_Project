<?php
require '../../config/db.php';

// Display customers
$stmt = $conn->prepare("
    SELECT *
    FROM customer
");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>

<?php include('../../templates/head.php'); ?>

<body>
    <?php include('../../templates/header.php'); ?>
    <section class="main-content">
        <div>
            <h2>All Customers:</h2>
        </div>
        <br>
        <div>
            <table border="1">
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>DOB</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Province</th>
                    <th>ZIP</th>
                </tr>
                <?php foreach ($customers as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['city']); ?></td>
                        <td><?php echo htmlspecialchars($row['state']); ?></td>
                        <td><?php echo htmlspecialchars($row['zip']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </section>
</body>

</html>
