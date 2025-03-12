<?php
session_start();
include '../includes/db.php';

// Ensure employer is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: employer_login.php");
    exit();
}

$employer_id = $_SESSION['employer_id'];
$job_id = '';
$title = '';
$category = '';
$location = '';
$salary = '';
$description = '';
$apply_link = '';

// Check if editing a job
if (isset($_GET['edit'])) {
    $job_id = $_GET['edit'];
    $query = "SELECT * FROM jobs WHERE id = '$job_id' AND employer_id = '$employer_id'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $job = mysqli_fetch_assoc($result);
        $title = $job['title'];
        $category = $job['category'];
        $location = $job['location'];
        $salary = $job['salary'];
        $description = $job['description'];
        $apply_link = $job['apply_link'];
    }
}

// Handle Job Posting & Editing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $apply_link = mysqli_real_escape_string($conn, $_POST['apply_link']);

    if (!empty($_POST['job_id'])) {
        // Update existing job
        $job_id = $_POST['job_id'];
        $query = "UPDATE jobs SET title='$title', category='$category', location='$location', salary='$salary', description='$description', apply_link='$apply_link' WHERE id='$job_id' AND employer_id='$employer_id'";
    } else {
        // Insert new job
        $query = "INSERT INTO jobs (employer_id, title, category, location, salary, description, apply_link) 
                  VALUES ('$employer_id', '$title', '$category', '$location', '$salary', '$description', '$apply_link')";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: employe_dashboard.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $job_id ? "Edit Job" : "Post a Job"; ?> - Employer Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center"><?php echo $job_id ? "Edit Job" : "Post a New Job"; ?></h2>
        <form action="post_job.php" method="POST">
            <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
            <div class="form-group">
                <label for="title">Job Title:</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($category); ?>" required>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" required>
            </div>
            <div class="form-group">
                <label for="salary">Salary:</label>
                <input type="number" step="0.01" class="form-control" id="salary" name="salary" value="<?php echo htmlspecialchars($salary); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Job Description:</label>
                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="form-group">
                <label for="apply_link">Application Link:</label>
                <input type="url" class="form-control" id="apply_link" name="apply_link" value="<?php echo htmlspecialchars($apply_link); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $job_id ? "Update Job" : "Post Job"; ?></button>
        </form>
    </div>
</body>
</html>
