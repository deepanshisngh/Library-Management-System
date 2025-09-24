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
// SQL query to get overdue issues
$today = date('Y-m-d');
$sql = "SELECT i.issue_id, b.title, b.serial_no, m.first_name, m.last_name, i.issue_date, i.return_date, b.category
        FROM issue_records i
        JOIN books b ON i.book_id = b.book_id
        JOIN members m ON i.member_id = m.member_id
        WHERE i.status = 'issued' AND i.return_date < '$today'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Overdue Returns</h2>";
    echo "<table border='1'>";
    echo "<tr>
            <th>Issue ID</th>
            <th>Serial No.</th>
            <th>Title</th>
            <th>Category</th>
            <th>Issued to</th>
            <th>Issue Date</th>
            <th>Due Date</th>
            <th>Overdue Days</th>
            <th>Fine Calculated (Rs.)</th>
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

        // Calculate overdue days and fine
        $overdue_days = floor( (strtotime($today) - strtotime($row['return_date'])) / (60 * 60 * 24) );
        $fine = $overdue_days * 10; // Assuming a fine of Rs. 10 per day

        echo "<td>" . $overdue_days . "</td>";
        echo "<td>" . number_format($fine, 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    $message = "<p>There are no overdue returns at the moment. All is well!</p>";
}

echo $message;
include("../includes/footer.php");
?>