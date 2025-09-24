<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$message = '';

if (isset($_POST['add_book'])) {
    $serial_no = $_POST['serial_no'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $status = $_POST['status'];
    $cost = $_POST['cost'];
    $procurement_date = $_POST['procurement_date'];

    // Check for mandatory fields
    if (empty($serial_no) || empty($title) || empty($author) || empty($category) || empty($cost) || empty($procurement_date)) {
        $message = "<p style='color: red;'>All fields are mandatory.</p>";
    } else {
        $sql = "INSERT INTO books (serial_no, title, author, category, status, cost, procurement_date) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssds", $serial_no, $title, $author, $category, $status, $cost, $procurement_date);
        
        if ($stmt->execute()) {
            $message = "<p style='color: green;'>Book/Movie added successfully!</p>";
        } else {
            $message = "<p style='color: red;'>Error adding book/movie: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}
?>

<div class="container">
    <h2>Add New Book or Movie</h2>
    <?php echo $message; ?>
    
    <form action="" method="post">
        <p>
            <label>Serial No:</label>
            <input type="text" name="serial_no" required>
        </p>
        <p>
            <label>Title:</label>
            <input type="text" name="title" required>
        </p>
        <p>
            <label>Author:</label>
            <input type="text" name="author" required>
        </p>
        <p>
            <label>Category:</label>
            <input type="text" name="category" required>
        </p>
        <p>
            <label>Status:</label>
            <select name="status" required>
                <option value="Available">Available</option>
                <option value="Not Available">Not Available</option>
                <option value="On Hold">On Hold</option>
            </select>
        </p>
        <p>
            <label>Cost:</label>
            <input type="number" name="cost" required>
        </p>
        <p>
            <label>Procurement Date:</label>
            <input type="date" name="procurement_date" required>
        </p>
        
        <button type="submit" name="add_book">Add Book/Movie</button>
    </form>
</div>

<?php include("../includes/footer.php"); ?>