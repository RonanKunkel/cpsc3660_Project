<?php
    require 'db.php';

    $vehicles = [];

    if (isset($_POST['show'])) {
        $stmt = $conn->query("SELECT * FROM vehicle");
        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>jonesautodb</title>
    </head>

    <body>
        <form method="POST">
            <button type="submit" name="show">Show Vehicles</button>
        </form>

        <?php if (!empty($vehicles)): ?>
            <table border="1">
                <tr>
                    <th>VIN</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Color</th>
                    <th>Interio Color</th>
                    <th>Miles</th>
                    <th>Style</th>
                    <th>Vehicle Condition</th>
                    <th>Book Price</th>
                </tr>

                <?php foreach ($vehicles as $v): ?>
                <tr>
                    <td><?php echo $v['vin']; ?></td>
                    <td><?php echo $v['make']; ?></td>
                    <td><?php echo $v['model']; ?></td>
                    <td><?php echo $v['year']; ?></td>
                    <td><?php echo $v['color']; ?></td>
                    <td><?php echo $v['interior_color']; ?></td>
                    <td><?php echo $v['miles']; ?></td>
                    <td><?php echo $v['style']; ?></td>
                    <td><?php echo $v['vehicle_condition']; ?></td>
                    <td><?php echo $v['book_price']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

    </body>
</html>