<?php
session_start();
include("../config/db.php");

if ($_SESSION['role'] == 'admin') {
    if (isset($_POST['add'])) {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $publisher = $_POST['publisher'];
        $year = $_POST['year'];

        $sql = "INSERT INTO books (title, author, publisher, year) VALUES ('$title','$author','$publisher','$year')";
        $conn->query($sql);
    }
}
$result = $conn->query("SELECT * FROM books");

echo "<h2>Books</h2>";
while ($row = $result->fetch_assoc()) {
    echo $row['title']." by ".$row['author']." (Available: ".$row['available'].")<br>";
}

if ($_SESSION['role'] == 'admin') {
?>
<form method="post">
    <input type="text" name="title" placeholder="Title" required>
    <input type="text" name="author" placeholder="Author">
    <input type="text" name="publisher" placeholder="Publisher">
    <input type="number" name="year" placeholder="Year">
    <button type="submit" name="add">Add Book</button>
</form>
<?php } ?>
