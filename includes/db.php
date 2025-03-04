<?php
$servername = "localhost";
$username = "root";  // Update this if you have a different DB username
$password = "";  // Update this if your MySQL has a password
$database = "job_portal"; // Update with your actual database name

// Create database connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");
?>
