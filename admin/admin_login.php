<?php
session_start();
include 'db.php';

define("ADMIN_EMAIL", "gayatripisya@gmail.com");  // Change to your admin email
define("ADMIN_HASHED_PASSWORD", "26265fd51f8850901201ef1e79dce080"); // MD5 of your password

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $hashed_input_password = md5($password); // Hash the input password using MD5

    if ($email === ADMIN_EMAIL && $hashed_input_password === ADMIN_HASHED_PASSWORD) {
        $_SESSION['admin_email'] = $email;
        header("Location: admin_dashboard.html");
        exit();
    } else {
        echo "<script>alert('❌ Invalid credentials!'); window.location.href='admin_login.html';</script>";
    }
}
?>
