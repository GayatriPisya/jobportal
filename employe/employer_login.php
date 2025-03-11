<?php
session_start();
require_once "../includes/db.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Fetch user from the database
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ? AND role = 'employer'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['employer_id'] = $id;
            $_SESSION['employer_name'] = $name;
            header("Location: employe_dashboard.html"); // Redirect to employer dashboard
            exit();
        } else {
            echo "Error: Incorrect password.";
        }
    } else {
        echo "Error: Employer not found.";
    }

    $stmt->close();
    $conn->close();
}
?>
