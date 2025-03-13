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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
    <style>
        .container {
            max-width: 600px;
            margin-top: 20px;
            padding: 20px;
        }
        .form-control {
            background-color:rgb(247, 247, 247);
            border: 1px solidrgb(158, 181, 203);
            border-radius: 0.25rem;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .btn-primary {
            align-items: center;
            background-color: #007bff;
            border-color:rgb(35, 51, 67);
            color: #fff;
        }
        .btn-primary:hover {
            background-color:rgb(0, 217, 0);
            border-color:rgb(31, 49, 69);
        }
        .btn-primary:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.5);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            font-weight: bold;
        }
        @media (max-width: 575px) {
            .container {
                margin-top: 20px;
            }
            .form-group {
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center"><i class="fas fa-edit mr-2"></i><?php echo $job_id ? "Edit Job" : "Post a New Job"; ?></h2>
        <form action="post_job.php" method="POST">
            <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
            <div class="form-group">
                <label for="title"><i class="fas fa-briefcase mr-2"></i>Job Title:</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div class="form-group">
                <label for="category"><i class="fas fa-tags mr-2"></i>Category:</label>
                <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($category); ?>" required>
            </div>
            <div class="form-group">
                <label for="location"><i class="fas fa-map-marker-alt mr-2"></i>Location:</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" required>
            </div>
            <div class="form-group">
                <label for="salary"><i class="fas fa-dollar-sign mr-2"></i>Salary:</label>
                <input type="number" step="0.01" class="form-control" id="salary" name="salary" value="<?php echo htmlspecialchars($salary); ?>" required>
            </div>
            <div class="form-group">
                <label for="description"><i class="fas fa-file-alt mr-2"></i>Job Description:</label>
                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="form-group">
                <label for="apply_link"><i class="fas fa-link mr-2"></i>Application Link:</label>
                
                <input type="url" class="form-control" id="apply_link" name="apply_link" value="<?php echo htmlspecialchars($apply_link); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $job_id ? "Update Job" : "Post Job"; ?></button>
        </form>
    </div>
</body>
</html>

