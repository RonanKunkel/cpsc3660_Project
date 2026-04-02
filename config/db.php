<?php

$host = "localhost";
$dbname = "jonesautodb";
$user = "root";
$pass = "";

try {
  $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Connection Failed: " . $e->getMessage());
}
