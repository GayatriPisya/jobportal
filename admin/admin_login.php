<?php
session_start();
include '../includes/db.php';  // Ensure the path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($email) || empty($password)) {
        echo "<script>alert('⚠️ All fields are required!'); window.location.href='admin_login.html';</script>";
        exit();
    }

    // Check if the admin exists
    $sql = "SELECT * FROM users WHERE email = ? AND role = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];  // Fetch hashed password from DB

        if (password_verify($password, $hashed_password)) {
            // ✅ Password Matched - Set Session and Redirect
            $_SESSION['admin_email'] = $row['email'];
            $_SESSION['admin_name'] = $row['name']; // Store admin name for dashboard
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "<script>alert('❌ Incorrect Password!'); window.location.href='admin_login.html';</script>";
        }
    } else {
        echo "<script>alert('❌ Admin not found!'); window.location.href='admin_login.html';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
