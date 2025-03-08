<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch admin user
    $sql = "SELECT * FROM users WHERE email = ? AND role = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Debugging - Check stored hash
        error_log("Stored Password Hash: " . $user['password']);

        // Verify the entered password
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_email'] = $user['email'];
            header("Location: admin_dashboard.php"); // Redirect to admin dashboard
            exit();
        } else {
            header("Location: admin_login.html?error=Invalid credentials");
            exit();
        }
    } else {
        header("Location: admin_login.html?error=Admin account not found");
        exit();
    }
}
?>
