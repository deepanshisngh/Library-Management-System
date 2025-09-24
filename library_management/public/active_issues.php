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
// SQL query to get active issues
$sql = "SELECT i.issue_id, b.title, b.serial_no, m.first_name, m.last_name, i.issue_date, i.return_date, b.category
        FROM issue_records i
        JOIN books b ON i.book_id = b.book_id
        JOIN members m ON i.member_id = m.member_id
        WHERE i.status = 'issued'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Active Issues</h2>";
    echo "<table border='1'>";
    echo "<tr>
            <th>Issue ID</th>
            <th>Serial No.</th>
            <th>Title</th>
            <th>Category</th>
            <th>Issued to</th>
            <th>Issue Date</th>
            <th>Due Date</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['issue_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['serial_no']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['issue_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['return_date']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    $message = "<p>There are no active issues at the moment.</p>";
}

echo $message;
include("../includes/footer.php");
?>