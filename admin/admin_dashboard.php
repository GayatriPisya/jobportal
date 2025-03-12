<?php
session_start();
include '../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.html");
    exit();
}

// Fetch total users, jobs, and applications
$userQuery = "SELECT COUNT(*) AS total_users FROM users WHERE role IN ('employer', 'job_seeker')";
$jobQuery = "SELECT COUNT(*) AS total_jobs FROM jobs";
$appQuery = "SELECT COUNT(*) AS total_apps FROM applications";

$userResult = $conn->query($userQuery);
$jobResult = $conn->query($jobQuery);
$appResult = $conn->query($appQuery);

$userCount = $userResult->fetch_assoc()['total_users'];
$jobCount = $jobResult->fetch_assoc()['total_jobs'];
$appCount = $appResult->fetch_assoc()['total_apps'];

// Fetch admin details
$adminQuery = "SELECT * FROM users WHERE id = '" . $_SESSION['admin_id'] . "'";
$adminResult = $conn->query($adminQuery);
$adminDetails = $adminResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <style>
        body { background-color: #f4f4f4; font-family: 'Poppins', sans-serif; }
        .sidebar { background-color: #211C84; width: 250px; height: 100vh; position: fixed; padding: 20px; }
        .sidebar a { color: white; display: block; padding: 10px; margin-bottom: 10px; text-decoration: none; border-radius: 8px; }
        .sidebar a:hover, .sidebar a.active { background: #4D55CC; }
        .main-content { margin-left: 260px; padding: 20px; }
        .card { border-radius: 10px; padding: 20px; text-align: center; }
        .card i { font-size: 2rem; margin-bottom: 10px; }
        .chart-container { width: 100%; height: 300px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="admin_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="manage_users.html"><i class="fas fa-users"></i> Manage Users</a>
        <a href="manage_jobs.html"><i class="fas fa-briefcase"></i> Manage Jobs</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <h2 class="text-center mb-4">Admin Dashboard</h2>
        <p class="text-center">Welcome, <strong><?php echo $adminDetails['name']; ?></strong></p>
        
        <div class="row text-center">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <i class="fas fa-users"></i>
                    <h5>Total Users</h5>
                    <p><?php echo $userCount; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <i class="fas fa-briefcase"></i>
                    <h5>Total Jobs</h5>
                    <p><?php echo $jobCount; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <i class="fas fa-file-alt"></i>
                    <h5>Total Applications</h5>
                    <p><?php echo $appCount; ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>