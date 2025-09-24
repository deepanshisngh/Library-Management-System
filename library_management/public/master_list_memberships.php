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
$result = $conn->query("SELECT * FROM members");

if ($result->num_rows > 0) {
    echo "<h2>Master List of Memberships</h2>";
    echo "<table border='1'>";
    echo "<tr>
            <th>Membership ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Contact</th>
            <th>Address</th>
            <th>Aadhar Card No.</th>
            <th>Start Date</th>
            <th>End Date</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['membership_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['contact_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['contact_address']) . "</td>";
        echo "<td>" . htmlspecialchars($row['aadhar_card_no']) . "</td>";
        echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['end_date']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    $message = "<p>No members found in the database.</p>";
}

echo $message;
include("../includes/footer.php");
?>