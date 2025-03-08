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

// Fetch admin details
$adminQuery = "SELECT * FROM users WHERE id = '".$_SESSION['admin_id']."'";
$adminResult = $conn->query($adminQuery);
$adminDetails = $adminResult->fetch_assoc();

// Fetch total users and applications growth month wise yearly
$growthQuery = "SELECT 
    YEAR(CURDATE()) - YEAR(created_at) AS year_diff, 
    MONTH(CURDATE()) - MONTH(created_at) AS month_diff, 
    COUNT(*) AS count 
FROM users 
WHERE role IN ('employer', 'job_seeker') 
GROUP BY YEAR(created_at), MONTH(created_at) 
ORDER BY created_at ASC";
$growthResult = $conn->query($growthQuery);
$growthData = array();
while ($row = $growthResult->fetch_assoc()) {
    $growthData[$row['year_diff']][$row['month_diff']] = $row['count'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Montserrat', sans-serif;
        }
        .dashboard-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }
        .sidebar {
            background-color: #2d4059;
            padding: 20px;
            border-radius: 12px 0px 0px 12px;
            height: 100vh;
            position: fixed;
            width: 250px;
            overflow-y: auto;
        }
        .sidebar a {
            color: #ffffff;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 12px;
            margin-bottom: 10px;
        }
        .sidebar a:hover {
            background-color: #3a4f62;
        }
        .sidebar a.active {
            background-color: #3a4f62;
        }
        .main-content {
            margin-left: 250px;
        }
        .card {
            margin-bottom: 25px;
            border: none;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            position: relative;
            padding: 25px;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .card-text {
            font-size: 2.5rem;
            font-weight: 700;
        }
        .btn-outline-primary, .btn-outline-success, .btn-danger {
            border-radius: 20px;
            font-weight: 600;
            padding: 10px 25px;
        }
        .chart-container {
            width: 100%;
            height: 300px;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>

    <div class="sidebar">
        <a href="admin_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="admin_profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
        <a href="manage_jobs.php"><i class="fas fa-briefcase"></i> Manage Jobs</a>
        <a href="admin_settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="dashboard-container">
            <h2 class="text-center mb-4">Admin Dashboard</h2>
            <p class="text-center mb-5">Welcome, <strong><?php echo $adminDetails['name']; ?></strong></p>

            <div class="row text-center">
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

            <div class="row">
                <div class="col-md-6">
                    <div class="chart-container">
                        <canvas id="usersChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-container">
                        <canvas id="appsChart"></canvas>
                    </div>
                </div>
            </div>

            <script>
                // Users Chart
                const usersData = <?php echo json_encode($growthData); ?>;
                const usersLabels = Object.keys(usersData);
                const usersValues = usersLabels.map(year => usersData[year].map(month => month).reduce((a, b) => a + b, 0));
                const usersChart = new Chart(document.getElementById("usersChart"), {
                    type: 'bar',
                    data: {
                        labels: usersLabels,
                        datasets: [{
                            label: 'Users Growth',
                            data: usersValues,
                            backgroundColor: 'rgba(0, 123, 255, 0.2)',
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Applications Chart
                const appsLabels = usersLabels;
                const appsValues = usersLabels.map(year => usersData[year].map(month => usersData[year][month]).reduce((a, b) => a + b, 0));
                const appsChart = new Chart(document.getElementById("appsChart"), {
                    type: 'bar',
                    data: {
                        labels: appsLabels,
                        datasets: [{
                            label: 'Applications Growth',
                            data: appsValues,
                            backgroundColor: 'rgba(40, 167, 69, 0.2)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        </div>
    </div>

</body>
</html>

