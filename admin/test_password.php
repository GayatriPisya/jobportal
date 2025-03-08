<?php
// Your newly generated hashed password
$stored_hash = '$2y$10$0M3ElyzaM6sfn/bNshKLFuVIZMxuhjvhLpu/e0kKvst0Pz.9/MY/2';

// Password you want to check
$entered_password = "admin123"; // The password you will enter in the login form

// Verify the entered password against the stored hash
if (password_verify($entered_password, $stored_hash)) {
    echo "✅ Password is correct!";
} else {
    echo "❌ Invalid password!";
}
?>
