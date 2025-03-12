<?php
session_start();
include '../includes/db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.html");
    exit();
}

// Handle job deletion
if (isset($_GET['delete'])) {
    $job_id = intval($_GET['delete']);
    $deleteQuery = "DELETE FROM jobs WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $job_id);
    
    if ($stmt->execute()) {
        header("Location: manage_jobs.html");
        exit();
    } else {
        echo "Error deleting job: " . $conn->error;
    }
}

// Fetch jobs and return JSON response for frontend
$query = "SELECT id, title, description, apply_link FROM jobs";
$result = $conn->query($query);

$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}

header('Content-Type: application/json');
echo json_encode($jobs);
?>
