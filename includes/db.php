<?php
$servername = "localhost";
$username = "root";  // Your MySQL username
$password = "";  // Your MySQL password (leave empty if no password)
$database = "job_portal";  // Your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");
?>
