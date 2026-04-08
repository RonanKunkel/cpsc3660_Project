<?php
require '../../config/db.php';
session_start();

$errors = '';
$success = false;

class Employer
{
    private string $name;
    private string $title;
    private string $super;
    private string $phone;
    private string $address;
    private string $start_date;

    public function __construct(array $_post)
    {
        $this->name = trim($_post['employer_name'] ?? '');
        $this->title = trim($_post['employer_title'] ?? '');
        $this->super = trim($_post['supervisor'] ?? '');
        $this->phone = trim($_post['supervisor_phone'] ?? '');
        $this->address = trim($_post['address'] ?? '');
        $this->start_date = trim($_post['start_date'] ?? '');
    }

    public function check_error_or_safe(): string
    {
        if (empty($this->name) || empty($this->title) || empty($this->super) || empty($this->address)) {
            return "Error, one or more form values is not defines or empty!";
        }
        if ($this->phone <= 0) {
            return "Error, invalid phone number entered";
        }
        return '';
    }

    public function _prepare($conn)
    {
        return $conn->prepare("
      INSERT INTO employment_history
      (customer_id, employer, title, supervisor, supervisor_phone, address, start_date)
      VALUES
      (:customer_id, :employer, :title, :supervisor, :supervisor_phone, :address, :start_date)
    ");
    }

    public function _execute($stmt)
    {
        $stmt->execute([
          ':customer_id' => $_SESSION['user_id'],
          ':employer' => $this->name,
          ':title' => $this->title,
          ':supervisor' => $this->super,
          ':supervisor_phone' => $this->phone,
          ':address' => $this->address,
          ':start_date' => $this->start_date,
        ]);
    }
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employer = new Employer($_POST);
    $error = $employer->check_error_or_safe();

    if (empty($error)) {
        try {
            $conn->beginTransaction();
            $stmt = $employer->_prepare($conn);
            $employer->_execute($stmt);
            $conn->commit();
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit;
        } catch (PDOException $e) {
            $conn->rollBack();
            $error = "Database error: " . $e->getMessage();
        }
    }
}

if (isset($_GET['success'])) {
    $success = true;
}
?>

<!DOCTYPE html>
<html>

<?php include('../../templates/head.php'); ?>

<body>
  <?php include('../../templates/customerHeader.php'); ?>
  <section class="main-content">
    <form method="POST">
      <h2>Add Employer</h2>

      <?php if ($success): ?>
        <p class="success">Employer Added!</p>
      <?php elseif ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <label for="employer_name">Employer Name</label>
      <input type="text" id="employer_name" name="employer_name" required><br><br>

      <label for="employer_title">Employer Title</label>
      <input type="text" id="employer_title" name="employer_title" required><br><br>

      <label for="supervisor">Supervisor</label>
      <input type="text" id="supervisor" name="supervisor" required><br><br>

      <label for="supervisor_phone">Supervisor Phone</label>
      <input type="tel" id="supervisor_phone" name="supervisor_phone" required><br><br>

      <label for="address">Address</label>
      <input type="text" id="address" name="address" required><br><br>

      <label for="start_date">Start Date</label>
      <input type="date" id="start_date" name="start_date" required><br><br>

      <button type="submit">Submit</button>

    </form>
  </section>

</body>

</html>
