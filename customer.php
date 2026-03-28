<?php
require 'db.php';

class Employer
{
  public readonly string $name;
  public readonly string $title;
  public readonly string $super;
  public readonly string $phone;
  public readonly string $address;
  public readonly string $start_date;

  public function __construct(array $_post)
  {
    $this->name = trim($_post['name'] ?? '');
    $this->title = trim($_post['title'] ?? '');
    $this->super = trim($_post['super'] ?? '');
    $this->phone = trim($_post['phone'] ?? '');
    $this->address = trim($_post['address'] ?? '');
    $this->start_date = trim($_post['start_date'] ?? '');
  } 

  public function _execute(int $customer_id, $stmt)
  {
    $stmt->execute([
      ':customer_id' => $customer_id,
      ':employer' => $this->name,
      ':title' => $this->title,
      ':supervisor' => $this->super,
      ':supervisor_phone' => $this->phone,
      ':address' => $this->address,
      ':start_date' => $this->start_date,
    ]);
  }
}

class Customer
{
  private string $first_name;
  private string $last_name;
  private string $phone; 
  private string $address; 
  private string $city;
  private string $state; 
  private string $zip;
  private string $gender; 
  private string $date_of_birth;
  private array $employment_history = [];

  public function __construct(array $_post)
  {
    $this->first_name = trim($_post['first_name'] ?? '');
    $this->last_name = trim($_post['last_name'] ?? '');
    $this->phone = trim($_post['phone'] ?? '');
    $this->address = trim($_post['address'] ?? '');
    $this->city = trim($_post['city'] ?? '');
    $this->state = trim($_post['state'] ?? '');
    $this->zip = trim($_post['zip'] ?? '');
    $this->gender = trim($_post['gender'] ?? '');
    $this->date_of_birth = trim($_post['date_of_birth'] ?? '');
  }

  public function setEmploymentHistory(array $employers)
  {
    foreach ($employers as $employer) {
      $this->employment_history[] = new Employer($employer);
    }
  }

  public function save($conn)
  {
    $stmt = $conn->prepare("
      INSERT INTO customer (last_name, first_name, gender, date_of_birth, phone, address, city, state, zip)
      VALUES (:last_name, :first_name, :gender, :date_of_birth, :phone, :address, :city, :state, :zip)
    ");
    $stmt->execute([
      ':last_name' => $this->last_name,
      ':first_name' => $this->first_name,
      ':gender' => $this->gender,
      ':date_of_birth' => $this->date_of_birth,
      ':phone' => $this->phone,
      ':address' => $this->address,
      ':city' => $this->city,
      ':state' => $this->state,
      ':zip' => $this->zip,
    ]);
    $customer_id = (int)$conn->lastInsertId();
    if (!empty($this->employment_history)) {
      $stmt = $conn->prepare("
      INSERT INTO employment_history (customer_id, employer, title, supervisor, supervisor_phone, address, start_date)
      VALUES (:customer_id, :employer, :title, :supervisor, :supervisor_phone, :address, :start_date)");
      foreach ($this->employment_history as $employer) {
        $employer->_execute($customer_id, $stmt);
      }
    }
  }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $customer = new Customer($_POST);
  $customer->setEmploymentHistory($_POST['employers'] ?? []);

  try {
    $conn->beginTransaction();
    $customer->save($conn);
    $conn->commit();

    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
    exit;
  } catch (PDOException $e) {
    $conn->rollBack();
    $error = "Database error: " . $e->getMessage();
  }
}

?>


<!DOCTYPE html>
<html>
<?php include('templates/header.php'); ?>
<body>
    <h1>Add Customer Details</h1>
    <h2>Personal Info</h2>
    <form method="POST"> 
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" maxlength="20" minlength="2" required><br><br>
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" maxlength="20" minlength="2" required><br><br>
        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="">Gender</option>
            <option value="Male">Man</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select><br><br>
        <label for="date_of_birth">Date of Birth</label>
        <input type="date" id="date_of_birth" name="date_of_birth" required><br><br>
        <label for="phone">Phone:</label>
        <input type="tel" id="phone" name="phone" required><br><br>
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" maxlength="50" required><br><br>
        <label for="city">City:</label>
        <input type="text" id="city" name="city" required><br><br>
        <label for="state">State:</label>
        <input type="text" id="state" name="state" required><br><br>
        <label for="zip">Zip/Postal Code:</label>
        <input type="text" id="zip" name="zip" maxlength="6" minlength="6" required><br><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
