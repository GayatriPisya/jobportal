<?php
session_start();
include '../includes/db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die(json_encode(["error" => "Unauthorized access."]));
}

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_user'])) {
    $user_id = intval($_POST['delete_user']);
    $delete_query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo "User deleted successfully.";
    } else {
        echo "Error deleting user.";
    }
    exit();
}

// Fetch all users
$query = "SELECT id, name, email, role FROM users WHERE role IN ('employer', 'job_seeker')";
$result = $conn->query($query);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Return users as JSON
header("Content-Type: application/json");
echo json_encode($users);
?>
