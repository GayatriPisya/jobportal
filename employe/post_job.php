<?php
session_start();
include '../db_connection.php';

// Ensure employer is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: employer_login.html");
    exit();
}

$employer_id = $_SESSION['employer_id'];

// Handle Job Posting
if (isset($_POST['post_job'])) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $salary = $_POST['salary'];
    $description = $_POST['description'];
    $apply_link = $_POST['apply_link'];

    $query = "INSERT INTO jobs (employer_id, title, category, location, salary, description, apply_link) 
              VALUES ('$employer_id', '$title', '$category', '$location', '$salary', '$description', '$apply_link')";

    if (mysqli_query($conn, $query)) {
        header("Location: employer_dashboard.html");
        exit();
    } else {
        echo "Error posting job: " . mysqli_error($conn);
    }
}

// Handle Job Deletion
if (isset($_GET['delete'])) {
    $job_id = $_GET['delete'];
    $query = "DELETE FROM jobs WHERE id = '$job_id' AND employer_id = '$employer_id'";

    if (mysqli_query($conn, $query)) {
        header("Location: employer_dashboard.html");
        exit();
    } else {
        echo "Error deleting job: " . mysqli_error($conn);
    }
}
?>
