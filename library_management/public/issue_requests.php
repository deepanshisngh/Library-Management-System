<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';
// SQL query to get pending issue requests
$sql = "SELECT i.request_id, m.first_name, m.last_name, b.title, b.category, i.requested_date
        FROM issue_requests i
        JOIN members m ON i.member_id = m.member_id
        JOIN books b ON i.book_id = b.book_id
        WHERE i.status = 'pending'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Pending Issue Requests</h2>";
    echo "<table border='1'>";
    echo "<tr>
            <th>Request ID</th>
            <th>Membership ID</th>
            <th>Name</th>
            <th>Title</th>
            <th>Category</th>
            <th>Requested Date</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['request_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['member_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . htmlspecialchars($row['requested_date']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    $message = "<p>There are no pending issue requests at the moment.</p>";
}

echo $message;
include("../includes/footer.php");
?>