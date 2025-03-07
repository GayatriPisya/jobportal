<?php
session_start();
include '../includes/db.php'; // Database connection

// Enable error reporting for debugging (Remove this in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = ? AND role = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_email'] = $user['email'];
            header("Location: admin_dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            header("Location: admin_login.html?error=Invalid credentials.");
            exit();
        }
    } else {
        header("Location: admin_login.html?error=Admin account not found.");
        exit();
    }
}
?>
