<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}
echo "<h2>Welcome Student</h2>";
echo "<a href='books.php'>View Books</a> | ";
echo "<a href='reports.php'>My Reports</a> | ";
echo "<a href='logout.php'>Logout</a>";
?>
