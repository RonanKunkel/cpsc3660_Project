<?php
require '../../config/db.php';

// Variable to display errors in html
$error = '';

// Variable to keep track of employers
$employers = [];

function clean_customer_id(mixed $conn, array $_get, string &$error)
{
    $temp_customer_id = trim($_get["customer_id"] ?? '');

    $customer_query = $conn->prepare("
    SELECT id
    FROM customer
    WHERE id = ?");
    $customer_query->execute([$temp_customer_id]);

    $customer = $customer_query->fetch(PDO::FETCH_ASSOC);

    if ($customer === false) {
        $error = "Error, customer not found";
        return -1;
    }

    return (int)$customer['id'];
}


function get_employers(mixed $conn, int $customer_id, string &$error): array
{
    $employer_query = $conn->prepare("
        SELECT *
        FROM employment_history AS eh
        WHERE eh.customer_id = ?");
    $employer_query->execute([$customer_id]);
    $employers = $employer_query->fetchAll(PDO::FETCH_ASSOC);

    if ($employers === false || empty($employers)) {
        $error = "Error, customer not found";
        return [-1];
    }
    return $employers;
}

// Making sure the customer has actually entered something:
if (!empty($_GET['customer_id'])) {
    $customer_id = clean_customer_id($conn, $_GET, $error);

    if (empty($error)) {
        $employers = get_employers($conn, $customer_id, $error);
    }
}


?>
<!DOCTYPE html>
<html>
<?php include('../../templates/head.php'); ?>

<body>
  <?php include('../../templates/header.php'); ?>
  <section class="main-content">
    <div>
      <h2>Select Customer</h2>
      <form method="GET">
        <label for="customer_id">Customer ID</label>
        <input type="text" id="customer_id" name="customer_id" required><br><br>
        <button type="submit">Submit</button>
        <a href="?"><button type="button">Reset</button></a>
      </form>
      <?php if (empty($error)): ?>
        <table border="1">
          <tr>
            <th>Employer</th>
            <th>Title</th>
            <th>Supervisor</th>
            <th>Supervisor Phone</th>
            <th>Address</th>
            <th>Start Date</th>
          </tr>
          <?php foreach ($employers as $employer): ?>
            <tr>
              <td><?php echo htmlspecialchars($employer['employer']); ?></td>
              <td><?php echo htmlspecialchars($employer['title']); ?></td>
              <td><?php echo htmlspecialchars($employer['supervisor']); ?></td>
              <td><?php echo htmlspecialchars($employer['supervisor_phone']); ?></td>
              <td><?php echo htmlspecialchars($employer['address']); ?></td>
              <td><?php echo htmlspecialchars($employer['start_date']); ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php else: ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
    </div>
    <br>
  </section>
</body>

</html>
