<?php
session_start();
include '../includes/db.php';

// Ensure employer is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: employer_login.php");
    exit();
}

$employer_id = $_SESSION['employer_id'];

// Fetch jobs posted by the employer
$query = "SELECT * FROM jobs WHERE employer_id = '$employer_id'";
$result = mysqli_query($conn, $query);

// Handle Job Deletion
if (isset($_GET['delete'])) {
    $job_id = $_GET['delete'];
    $delete_query = "DELETE FROM jobs WHERE id = '$job_id' AND employer_id = '$employer_id'";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: employer_dashboard.php");
        exit();
    } else {
        echo "Error deleting job: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Employer Dashboard</h2>

        <!-- Button to Post a Job -->
        <a href="post_job.php" class="btn btn-success mb-3">Post a New Job</a>

        <h3>Your Job Listings</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Salary</th>
                    <th>Apply Link</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($job = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                        <td><?php echo htmlspecialchars($job['category']); ?></td>
                        <td><?php echo htmlspecialchars($job['location']); ?></td>
                        <td>$<?php echo number_format($job['salary'], 2); ?></td>
                        <td><a href="<?php echo htmlspecialchars($job['apply_link']); ?>" target="_blank">Apply Here</a></td>
                        <td>
                            <a href="post_job.php?edit=<?php echo $job['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="employer_dashboard.php?delete=<?php echo $job['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this job?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
