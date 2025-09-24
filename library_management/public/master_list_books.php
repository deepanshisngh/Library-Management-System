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
$result = $conn->query("SELECT * FROM books WHERE category = 'book'");

if ($result->num_rows > 0) {
    echo "<h2>Master List of Books</h2>";
    echo "<table border='1'>";
    echo "<tr>
            <th>Serial No.</th>
            <th>Name of Book</th>
            <th>Author Name</th>
            <th>Category</th>
            <th>Status</th>
            <th>Cost</th>
            <th>Procurement Date</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['serial_no']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['author_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>" . htmlspecialchars($row['cost']) . "</td>";
        echo "<td>" . htmlspecialchars($row['procurement_date']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    $message = "<p>No books found in the database.</p>";
}

echo $message;
include("../includes/footer.php");
?>