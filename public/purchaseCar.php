<?php if (isset($_GET['success'])): ?>
  <p class="success">Vehicle purchased and saved successfully!</p>
<?php endif; ?>
<?php

require '../config/db.php';


$vin = $make = $model = $year = $color = $interior_color = $miles = $style = $condition = $book_price = '';
$price_paid = $date = $location = $seller = $auction = '';
$problems = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vehicle
    $vin = trim($_POST['vin'] ?? '');
    $make = trim($_POST['make'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $year = (int) ($_POST['year'] ?? 0);
    $color = trim($_POST['color'] ?? '');
    $interior_color = trim($_POST['interior_color'] ?? '');
    $miles = (int) ($_POST['miles'] ?? 0);
    $style = trim($_POST['style'] ?? '');
    $condition = trim($_POST['condition'] ?? '');
    $book_price = (float) ($_POST['book_price'] ?? 0);

    // Purchase
    $price_paid = (float) ($_POST['price_paid'] ?? 0);
    $date = trim($_POST['date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $seller = trim($_POST['seller'] ?? '');
    $auction = trim($_POST['auction'] ?? '');

    // Problem
    if (!empty($_POST['problems'])) {
        foreach ($_POST['problems'] as $p) {
            $problems[] = [
              'description' => trim($p['description'] ?? ''),
              'estimated_cost' => (float) ($p['estimated_cost'] ?? 0),
              'actual_cost' => (float) ($p['actual_cost'] ?? 0),
            ];
        }
    }

    // Make sure fields are entered correctly
    if (empty($vin) || strlen($vin) !== 17) {
        $error = "VIN must be exactly 17 characters.";
    } elseif (empty($make) || empty($model) || empty($year)) {
        $error = "Make, Model, and Year are required.";
    } elseif (empty($condition) || empty($style)) {
        $error = "Condition and Style are required.";
    } elseif (empty($date)) {
        $error = "Purchase date is required.";
    } else {
        try {
            $conn->beginTransaction();

            // Add new vehicle
            $stmt = $conn->prepare("
                INSERT INTO vehicle (vin, make, model, year, color, interior_color, miles, style, vehicle_condition, book_price)
                VALUES (:vin, :make, :model, :year, :color, :interior_color, :miles, :style, :vehicle_condition, :book_price)
            ");
            $stmt->execute([
              ':vin' => $vin,
              ':make' => $make,
              ':model' => $model,
              ':year' => $year,
              ':color' => $color,
              ':interior_color' => $interior_color,
              ':miles' => $miles,
              ':style' => $style,
              ':vehicle_condition' => $condition,
              ':book_price' => $book_price,
            ]);

            // Add new purchase
            $stmt = $conn->prepare("
                INSERT INTO purchase (vin, purchase_date, location, auction, seller, price_paid)
                VALUES (:vin, :purchase_date, :location, :auction, :seller, :price_paid)
            ");
            $stmt->execute([
              ':vin' => $vin,
              ':purchase_date' => $date,
              ':location' => $location,
              ':auction' => $auction,
              ':seller' => $seller,
              ':price_paid' => $price_paid,
            ]);

            // get last id inserted
            $purchase_id = $conn->lastInsertId();

            // add each vehicle problem into repair table
            if (!empty($problems)) {
                $stmt = $conn->prepare("
                    INSERT INTO repair (purchase_id, description, estimated_cost, actual_cost)
                    VALUES (:purchase_id, :description, :estimated_cost, :actual_cost)
                ");
                foreach ($problems as $problem) {
                    $stmt->execute([
                      ':purchase_id' => $purchase_id,
                      ':description' => $problem['description'],
                      ':estimated_cost' => $problem['estimated_cost'],
                      ':actual_cost' => $problem['actual_cost'],
                    ]);
                }
            }

            $conn->commit();
            $success = true;

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
<?php include('../templates/head.php'); ?>

<body>
  <?php include('../templates/header.php'); ?>
  <section class="main-content">

    <?php if (isset($_GET['success'])): ?>
      <p class="success">Vehicle purchased and saved successfully!</p>
    <?php elseif ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
      <p class="success">Vehicle purchased and saved successfully!</p>
    <?php elseif ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
      <h1>Enter Car Purchase Details</h1>

      <h2>Vehicle Info</h2>

      <label for="vin">VIN:</label>
      <input type="text" id="vin" name="vin" maxlength="17" minlength="17" required><br><br>

      <label for="make">Make:</label>
      <input type="text" id="make" name="make" maxlength="30" required><br><br>

      <label for="model">Model:</label>
      <input type="text" id="model" name="model" maxlength="30" required><br><br>

      <label for="year">Year:</label>
      <input type="number" id="year" name="year" min="1900" max="2100" required><br><br>

      <label for="color">Color:</label>
      <input type="text" id="color" name="color" maxlength="20" required><br><br>

      <label for="interior_color">Interior Color:</label>
      <input type="text" id="interior_color" name="interior_color" maxlength="20"><br><br>

      <label for="miles">Miles:</label>
      <input type="number" id="miles" name="miles" min="0" required><br><br>

      <label for="style">Style:</label>
      <select id="style" name="style" required>
        <option value="">Select Style</option>
        <option value="Coupe">Coupe</option>
        <option value="Sedan">Sedan</option>
        <option value="Hatchback">Hatchback</option>
        <option value="Pickup">Pickup</option>
        <option value="Van">Van</option>
        <option value="SUV">SUV</option>
        <option value="Wagon">Wagon</option>
      </select><br><br>

      <label for="condition">Condition:</label>
      <select id="condition" name="condition" required>
        <option value="">Select Condition</option>
        <option value="Excellent">Excellent</option>
        <option value="Light Wear">Light Wear</option>
        <option value="Moderate Wear">Moderate Wear</option>
        <option value="Abused">Abused</option>
      </select><br><br>

      <label for="book_price">Book Price:</label>
      <input type="number" id="book_price" name="book_price" step="0.01" min="0" required><br><br>

      <h2>Purchase Info</h2>

      <label for="price_paid">Price Paid:</label>
      <input type="number" id="price_paid" name="price_paid" step="0.01" min="0" required><br><br>

      <label for="date">Date:</label>
      <input type="date" id="date" name="date" required><br><br>

      <label for="location">Location:</label>
      <input type="text" id="location" name="location" maxlength="50" required><br><br>

      <label for="seller">Seller/Dealer:</label>
      <input type="text" id="seller" name="seller" maxlength="30" required><br><br>

      <label for="auction">Auction:</label>
      <input type="text" id="auction" name="auction" maxlength="30" placeholder="Auction name or leave blank"><br><br>

      <h2>Problems</h2>
      <div id="problems-container"></div>
      <button type="button" id="add-problem-btn">Add Problem</button>
      <br><br>

      <button type="submit">Submit</button>
    </form>

    <script>
      let problemCount = 0;
      const container = document.getElementById('problems-container');

      document.getElementById('add-problem-btn').addEventListener('click', function() {
        const index = problemCount++;
        const div = document.createElement('div');
        div.className = 'problem-entry';
        div.innerHTML = `
                <button type="button" class="remove-btn" onclick="this.parentElement.remove()">&times;</button>
                <label>Problem Description:</label>
                <input type="text" name="problems[${index}][description]" maxlength="100" required><br><br>
                <label>Est. Repair Cost:</label>
                <input type="number" name="problems[${index}][estimated_cost]" step="0.01" min="0" required><br><br>
                <label>Actual Repair Cost:</label>
                <input type="number" name="problems[${index}][actual_cost]" step="0.01" min="0" required><br><br>
            `;
        container.appendChild(div);
      });
    </script>
  </section>
  <?php include('../templates/footer.php'); ?>
</body>

<?php if (isset($_GET['success'])): ?>
  <p class="success">Vehicle purchased and saved successfully!</p>
<?php elseif ($error): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

</html>
