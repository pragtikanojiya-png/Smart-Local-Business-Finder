<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "smart_local_business";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
