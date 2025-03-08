<?php
session_start();
include '../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.html");
    exit();
}

// Fetch total users
$userQuery = "SELECT COUNT(*) AS total_users FROM users WHERE role IN ('employer', 'job_seeker')";
$userResult = $conn->query($userQuery);
$userCount = $userResult->fetch_assoc()['total_users'];

// Fetch total jobs
$jobQuery = "SELECT COUNT(*) AS total_jobs FROM jobs";
$jobResult = $conn->query($jobQuery);
$jobCount = $jobResult->fetch_assoc()['total_jobs'];

// Fetch total applications
$appQuery = "SELECT COUNT(*) AS total_apps FROM applications";
$appResult = $conn->query($appQuery);
$appCount = $appResult->fetch_assoc()['total_apps'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="dashboard-container">
            <h2 class="text-center">Admin Dashboard</h2>
            <p class="text-center">Welcome, <strong><?php echo $_SESSION['admin_email']; ?></strong></p>

            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-users"></i> Total Users</h5>
                            <p class="card-text"><?php echo $userCount; ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-briefcase"></i> Total Jobs</h5>
                            <p class="card-text"><?php echo $jobCount; ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-file-alt"></i> Total Applications</h5>
                            <p class="card-text"><?php echo $appCount; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="manage_users.html" class="btn btn-outline-primary">Manage Users</a>
                <a href="manage_jobs.html" class="btn btn-outline-success">Manage Jobs</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>

</body>
</html>
