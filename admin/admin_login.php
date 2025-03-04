<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ? AND role = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password']; // Fetch hashed password from DB

        echo "Stored Hashed Password: " . $hashed_password . "<br>";

        if (password_verify($password, $hashed_password)) {
            echo "✅ Password Matched!";
        } else {
            echo "❌ Incorrect Password!";
        }
    } else {
        echo "❌ Admin not found!";
    }

    $stmt->close();
}
$conn->close();
?>
