<?php
include '../includes/db.php';

if (isset($_POST['keyword'])) {
    $keyword = '%' . $_POST['keyword'] . '%';
    
    $query = "SELECT * FROM jobs WHERE title LIKE ? OR description LIKE ? OR category LIKE ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='card mb-3'>
                    <div class='card-body'>
                        <h5 class='card-title'>{$row['title']}</h5>
                        <p class='card-text'>{$row['description']}</p>
                        <p><strong>Category:</strong> {$row['category']}</p>
                        <p><strong>Location:</strong> {$row['location']}</p>
                        <p><strong>Salary:</strong> \${$row['salary']}</p>
                        <a href='{$row['apply_link']}' target='_blank' class='btn btn-success'>Apply Now</a>
                    </div>
                  </div>";
        }
    } else {
        echo "<p>No jobs found.</p>";
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo "<p>Invalid request.</p>";
}
?>
