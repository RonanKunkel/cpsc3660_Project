<?php
require '../../config/db.php';
session_start();

$firstName = '';
$lastName = '';

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT firstName, lastName FROM employee WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $firstName = $user['firstName'];
        $lastName = $user['lastName'];
    }
}
?>
<!DOCTYPE html>
<html>

<?php include('../../templates/head.php'); ?>

<body>
    <?php include('../../templates/header.php'); ?>
    <section class="main-content">
        <h2>Welcome <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?>!</h2>
    </section>

</body>

</html>
