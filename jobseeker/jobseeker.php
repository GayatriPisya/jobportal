<?php
include '../includes/db.php';

if (isset($_POST['keyword']) && !empty(trim($_POST['keyword']))) {
    $keyword = '%' . trim($_POST['keyword']) . '%';

    $query = "SELECT * FROM jobs WHERE title LIKE ? OR description LIKE ? OR category LIKE ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='card mb-3'>
                    <div class='card-body'>
                        <h5 class='card-title'><i class='fas fa-briefcase mr-1'></i>&nbsp;{$row['title']}</h5>
                        <p class='card-text'><i class='fas fa-file-alt mr-1'></i>&nbsp;{$row['description']}</p>
                        <p><i class='fas fa-tags mr-1'></i>&nbsp;<strong>Category:</strong> {$row['category']}</p>
                        <p><i class='fas fa-map-marker-alt mr-1'></i>&nbsp;<strong>Location:</strong> {$row['location']}</p>
                        <p><i class='fas fa-dollar-sign mr-1'></i>&nbsp;<strong>Salary:</strong> \${$row['salary']}</p>
                        <a href='{$row['apply_link']}' target='_blank' class='btn btn-success'><i class='fas fa-external-link-alt mr-1'></i>&nbsp;Apply Now</a>
                    </div>
                  </div>";
        }
    } else {
        echo "<p>No jobs found.</p>";
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo "<p>Please enter a valid keyword to search.</p>";
}
?>

