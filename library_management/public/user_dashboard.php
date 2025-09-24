<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<div class="container">
    <h2>User Dashboard</h2>
    <p>Welcome to the Library Management System.</p>
    <p>From here, you can check the library's catalog.</p>
    <hr>
    <h3>Search & Reports</h3>
    <ul>
        <li><a href="search_books.php">Search for Books & Movies</a></li>
        <li><a href="master_list_books.php">View All Books & Movies</a></li>
    </ul>
</div>

<?php include("../includes/footer.php"); ?>