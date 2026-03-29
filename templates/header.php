<head>
  <title>Jones Auto</title>
  <style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

header {
  padding: 1em;
  display: flex;
  flex-wrap: wrap;
  flex-direction: row;
  background: #6164B1;
  justify-content: space-between;
}

.navbar-container {
  padding: 4px;
}
</style>
</head> 
<header>
  <div class="navbar-container">
    <h1>Jones Auto</h1>
  </div>
  <div>
    <nav class="navbar-container"> 
      <button onclick="window.location.href='purchaseCar.php'">Purchase Vehicle</button>
      <button onclick="window.location.href='carSales.php'">Sell A Car</button>
      <button onclick="window.location.href='customer.php'">Add a Customer</button>
      <button onclick="window.location.href='index.php'">Home</button>
      <button onclick="window.location.href='payments.php'">Add Customer Payments</button>
  </nav>
  </div>
</header>
