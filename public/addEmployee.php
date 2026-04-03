<?php
require '../config/db.php';

// employee
$first_name = $last_name = $phone  = '';
$success = false;
$error = '';

if (isset($_GET['success'])) {
    $success = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // sale
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');


    if ($first_name === '' || $last_name === '' || $phone === '') {
        $error = "You must fill every field.";
    } else {
        try {
            $conn->beginTransaction();

            // Add new employee
            $stmt = $conn->prepare("
                INSERT INTO employee (firstName, lastName, phone) 
                VALUES (:first_name, :last_name, :phone)
            ");
            $stmt->execute([
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':phone' => $phone,
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

<?php include('../templates/head.php'); ?>

<body>

    <?php include('../templates/header.php'); ?>
    <section class="main-content">
        <?php if ($success): ?>
            <p class="success">Employee Added!</p>
        <?php elseif ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <h2>Enter New Employee:</h2>

            <h3>Identifiers</h3>

            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required><br><br>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required><br><br>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required><br><br>

            <button type="submit">Submit</button>
        </form>
    </section>

</body>

</html>
