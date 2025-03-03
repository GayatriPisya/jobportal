<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        // Step 1: Send Password Reset Link
        $email = trim($_POST['email']);

        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND role = 'admin'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Generate token
            $token = bin2hex(random_bytes(50));
            $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Store token in DB
            $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $stmt->bind_param("sss", $token, $expires_at, $email);
            $stmt->execute();

            // Send reset link via email
            $reset_link = "http://yourwebsite.com/admin/forgot_password.php?token=$token";
            $subject = "Reset Your Password";
            $message = "Click the link below to reset your password:\n$reset_link";
            $headers = "From: noreply@yourwebsite.com";

            if (mail($email, $subject, $message, $headers)) {
                echo "Password reset link sent!";
            } else {
                echo "Failed to send email!";
            }
        } else {
            echo "No admin account found!";
        }
    } elseif (isset($_POST['token']) && isset($_POST['new_password'])) {
        // Step 2: Reset Password
        $token = $_POST['token'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            die("Passwords do not match!");
        }

        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Verify token
        $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id);
            $stmt->fetch();

            // Update password and clear token
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $id);

            if ($stmt->execute()) {
                echo "Password updated successfully! <a href='admin_login.html'>Login Here</a>";
            } else {
                echo "Error updating password!";
            }
        } else {
            echo "Invalid or expired token!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="forgot-password-container">
        <?php if (isset($_GET['token'])) { ?>
            <h2>Reset Your Password</h2>
            <form action="forgot_password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Reset Password</button>
            </form>
        <?php } else { ?>
            <h2>Enter Your Email</h2>
            <form action="forgot_password.php" method="POST">
                <input type="email" name="email" placeholder="Enter your admin email" required>
                <button type="submit">Send Reset Link</button>
            </form>
        <?php } ?>
    </div>
</body>
</html>
