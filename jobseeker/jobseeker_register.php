<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into the database
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'job_seeker')");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                header("Location: jobseeker_login.html?success=Registration successful. Please login.");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
    
    if (isset($error)) {
        header("Location: jobseeker_register.html?error=" . urlencode($error));
        exit();
    }
}
?>
