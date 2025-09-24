<?php
session_start();
include("../config/db.php");
include("../includes/header.php"); // Assuming you have a header.php file

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<div class="container">
    <h2>Reports</h2>
    <div class="nav">
        <a href="reports.php?type=books">Master List of Books</a>
        <a href="reports.php?type=memberships">Master List of Memberships</a>
        <a href="reports.php?type=active_issues">Active Issues</a>
        <a href="reports.php?type=overdue">Overdue Returns</a>
    </div>

    <?php
    $report_type = $_GET['type'] ?? 'books';
    
    switch ($report_type) {
        case 'books':
            echo "<h3>Master List of Books</h3>";
            $sql = "SELECT serial_no, title, author, category, status, cost, procurement_date FROM books WHERE category != 'Movie'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<table><tr><th>Serial No</th><th>Title</th><th>Author</th><th>Category</th><th>Status</th><th>Cost</th><th>Procurement Date</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row['serial_no'] . "</td><td>" . $row['title'] . "</td><td>" . $row['author'] . "</td><td>" . $row['category'] . "</td><td>" . $row['status'] . "</td><td>" . $row['cost'] . "</td><td>" . $row['procurement_date'] . "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No books found.";
            }
            break;

        case 'memberships':
            echo "<h3>Master List of Memberships</h3>";
            $sql = "SELECT membership_id, first_name, last_name, contact_number, contact_address, aadhar_card_no, start_date, end_date, status FROM memberships";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                 echo "<table><tr><th>ID</th><th>Name</th><th>Contact</th><th>Address</th><th>Aadhar</th><th>Start Date</th><th>End Date</th><th>Status</th></tr>";
                 while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row['membership_id'] . "</td><td>" . $row['first_name'] . " " . $row['last_name'] . "</td><td>" . $row['contact_number'] . "</td><td>" . $row['contact_address'] . "</td><td>" . $row['aadhar_card_no'] . "</td><td>" . $row['start_date'] . "</td><td>" . $row['end_date'] . "</td><td>" . $row['status'] . "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No memberships found.";
            }
            break;
            
        case 'active_issues':
            echo "<h3>Active Issues</h3>";
            $sql = "SELECT b.serial_no, b.title, m.membership_id, i.issue_date FROM issue_records i JOIN books b ON i.book_id = b.book_id JOIN users u ON i.user_id = u.user_id JOIN memberships m ON u.username = m.aadhar_card_no WHERE i.status = 'issued'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                 echo "<table><tr><th>Serial No</th><th>Book Title</th><th>Membership ID</th><th>Issue Date</th></tr>";
                 while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row['serial_no'] . "</td><td>" . $row['title'] . "</td><td>" . $row['membership_id'] . "</td><td>" . $row['issue_date'] . "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No active issues found.";
            }
            break;

        case 'overdue':
             echo "<h3>Overdue Returns</h3>";
             $sql = "SELECT b.serial_no, b.title, m.membership_id, i.issue_date, i.return_date FROM issue_records i JOIN books b ON i.book_id = b.book_id JOIN users u ON i.user_id = u.user_id JOIN memberships m ON u.username = m.aadhar_card_no WHERE i.status = 'issued' AND i.return_date < CURDATE()";
             $result = $conn->query($sql);
             if ($result->num_rows > 0) {
                echo "<table><tr><th>Serial No</th><th>Book Title</th><th>Membership ID</th><th>Issue Date</th><th>Return Date</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row['serial_no'] . "</td><td>" . $row['title'] . "</td><td>" . $row['membership_id'] . "</td><td>" . $row['issue_date'] . "</td><td>" . $row['return_date'] . "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No overdue returns found.";
            }
            break;
        
        // Add other cases for movies and pending requests
    }
    ?>

</div>

<?php
include("../includes/footer.php");
?>
