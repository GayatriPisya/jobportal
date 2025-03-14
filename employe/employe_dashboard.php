<?php
session_start();
include '../includes/db.php';

// Ensure employer is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: employer_login.php");
    exit();
}

$employer_id = $_SESSION['employer_id'];

// Handle Job Deletion
if (isset($_GET['delete'])) {
    $job_id = $_GET['delete'];

    // Check if job exists
    $check_query = "SELECT id FROM jobs WHERE id = ? AND employer_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $job_id, $employer_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $delete_query = "DELETE FROM jobs WHERE id = ? AND employer_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("ii", $job_id, $employer_id);
        if ($delete_stmt->execute()) {
            header("Location: employe_dashboard.php");
            exit();
        } else {
            echo "Error deleting job: " . $conn->error;
        }
    } else {
        echo "<script>alert('Invalid Job ID or unauthorized action.');</script>";
    }
}

// Fetch jobs posted by the employer
$query = "SELECT * FROM jobs WHERE employer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        body {
            background-color: #fff;
            font-family: 'Open Sans', sans-serif;
        }
        .navbar {
            background-color:rgb(9, 10, 49);
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #fff;
        }
        .container {
            margin-top: 30px;
        }
        .table-responsive {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        .table th, .table td {
            vertical-align: middle;
            border-color: #1A202C;
        }
        .btn {
            margin-bottom: 10px;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-primary {
            background-color: #1A202C;
            border-color: #1A202C;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color:rgb(10, 26, 56);
            color: #fff;
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            margin-bottom: 10px;
        }
        .card-text {
            color: #6c757d;
        }
        @media (max-width: 767px) {
            .table-responsive {
                overflow-x: scroll;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="/jobportal/index.html">
            <i class="fas fa-briefcase mr-2"></i>
            Job Portal - Employer Dashboard
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="employer_login.html"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h2 class="text-center mb-4"><i class="fas fa-suitcase mr-2"></i>Manage Your Jobs</h2>

        <!-- Button to Post a Job -->
        <a href="post_job.php" class="btn btn-primary mb-3 float-right"><i class="fas fa-plus-circle"></i> Post a New Job</a>

        <div class="clearfix"></div>

        <h3 class="mt-5">Your Job Listings</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="thead-dark">
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
                    <?php while ($job = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($job['title']); ?></td>
                            <td><?php echo htmlspecialchars($job['category']); ?></td>
                            <td><?php echo htmlspecialchars($job['location']); ?></td>
                            <td>$<?php echo number_format($job['salary'], 2); ?></td>
                            <td><a href="<?php echo htmlspecialchars($job['apply_link']); ?>" target="_blank"><i class="fas fa-external-link-alt"></i> Apply Here</a></td>
                            <td>
                                <a href="post_job.php?edit=<?php echo $job['id']; ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                <a href="employe_dashboard.php?delete=<?php echo $job['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this job?');"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

