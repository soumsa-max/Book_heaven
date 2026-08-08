<?php
// Database Configuration

$host = "localhost";
$username = "root";
$password = "";
$database = "book_heaven";

// Create Connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check Connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set Character Encoding
mysqli_set_charset($conn, "utf8");
?>