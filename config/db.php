<?php
$servername = "localhost";
$username = "root"; // Change as per your database credentials
$password = "";
$database = "maktab-a-ekra"; // Change to your DB name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
