<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.html");
    exit();
}

$jobs = $conn->query("SELECT * FROM jobs");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Jobs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Manage Jobs</h2>
        <table class="table table-bordered">
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
            <?php while ($job = $jobs->fetch_assoc()) { ?>
            <tr>
                <td><?= $job['title'] ?></td>
                <td><?= $job['category'] ?></td>
                <td><?= $job['location'] ?></td>
                <td>
                    <a href="edit_job.php?id=<?= $job['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete_job.php?id=<?= $job['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
