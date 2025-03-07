<?php
$host = "localhost";
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$database = "job_portal";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
