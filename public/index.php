<?php
include '../config/db.php';
session_start();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_type = $_POST['user_type'];
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;

    if ($user_type == 'customer' || $user_type == 'employee') {
        if (!$user_id) {
            $error = "Please enter a valid ID.";
        } else {
            $table = $user_type == 'customer' ? 'customer' : 'employee';
            $stmt = $conn->prepare("SELECT id FROM $table WHERE id = ?");
            $stmt->execute([$user_id]);
            if ($stmt->fetch()) {
                $_SESSION['user_type'] = $user_type;
                $_SESSION['user_id'] = $user_id;
                if ($user_type === 'customer') {
                    header("Location: ../public/view/customerHome.php");
                    exit;
                }

                if ($user_type === 'employee') {
                    header("Location: ../public/view/employeeHome.php");
                    exit;
                }
                exit;
            } else {
                $error = "Invalid $user_type ID.";
            }
        }
    } elseif ($user_type == 'admin') {
        $_SESSION['user_type'] = 'admin';
        header("Location: ../public/view/adminHome.php");
        exit;
    } else {
        $error = "Please select a user type.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../templates/head.php'; ?>

<body>
    <section id="heading">
        <header>
            <div class="navbar-container">
                <h1>Jones Auto - Login</h1>
            </div>
        </header>
    </section>

    <main style="padding: 20px;">
        <h2>Login</h2>
        <?php if (isset($error)) {
            echo "<p style='color: red;'>$error</p>";
        } ?>
        <form method="post">
            <label>
                <input type="radio" name="user_type" value="customer" onclick="showIdInput()"> Customer
            </label><br>
            <label>
                <input type="radio" name="user_type" value="employee" onclick="showIdInput()"> Employee
            </label><br>
            <label>
                <input type="radio" name="user_type" value="admin" onclick="hideIdInput()"> Admin
            </label><br><br>

            <div id="id_input" style="display: none;">
                <label for="user_id">ID:</label>
                <input type="number" name="user_id" id="user_id" required>
            </div><br>

            <button type="submit">Login</button>
        </form>
    </main>

    <script>
        function showIdInput() {
            document.getElementById('id_input').style.display = 'block';
            document.getElementById('user_id').required = true;
        }

        function hideIdInput() {
            document.getElementById('id_input').style.display = 'none';
            document.getElementById('user_id').required = false;
        }
    </script>
</body>

</html>
