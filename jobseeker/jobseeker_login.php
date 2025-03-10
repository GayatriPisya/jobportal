<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Check if user exists and is a job seeker
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ? AND role = 'job_seeker'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['jobseeker_id'] = $user['id'];
                $_SESSION['jobseeker_name'] = $user['name'];
                header("Location: jobseeker_dashboard.html");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "No account found or invalid role.";
        }
    } else {
        $error = "Please fill in all fields.";
    }

    if (isset($error)) {
        header("Location: jobseeker_login.html?error=" . urlencode($error));
        exit();
    }
}
?>
