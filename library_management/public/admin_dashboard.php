<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>

<div class="container">
    <h2>Admin Dashboard</h2>
    <p>Welcome, Admin! This is your dashboard.</p>
    <p>From here, you can manage the library's operations.</p>
    <hr>
    <h3>Maintenance</h3>
    <ul>
        <li><a href="add_book.php">Add New Book/Movie</a></li>
        <li><a href="update_book.php">Update/Remove Book/Movie</a></li>
        <li><a href="add_membership.php">Add New Member</a></li>
        <li><a href="update_membership.php">Update/Remove Member</a></li>
        <li><a href="user_management.php">Manage System Users</a></li>
    </ul>
    <hr>
    <h3>Transactions</h3>
    <ul>
        <li><a href="book_issue.php">Issue Book/Movie</a></li>
        <li><a href="return_book.php">Return Book/Movie</a></li>
        <li><a href="pay_fine.php">Pay Fine</a></li>
    </ul>
    <hr>
    <h3>Reports</h3>
    <ul>
        <li><a href="master_list_books.php">Master List of Books/Movies</a></li>
        <li><a href="master_list_memberships.php">Master List of Members</a></li>
        <li><a href="active_issues.php">Currently Issued Items</a></li>
        <li><a href="overdue_returns.php">Overdue Returns</a></li>
        <li><a href="issue_requests.php">Pending Issue Requests</a></li>
    </ul>
</div>

<?php include("../includes/footer.php"); ?>