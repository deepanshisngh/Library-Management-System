<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

// Check if the user is an admin; if not, redirect to the login page
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';
$item_details = null;

// Search for the item
if (isset($_POST['search_item'])) {
    $serial_no = $_POST['serial_no'];
    $sql = "SELECT * FROM books WHERE serial_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $serial_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $item_details = $result->fetch_assoc();
    $stmt->close();

    if (!$item_details) {
        $message = "<p style='color: red;'>Book/Movie with this Serial No. was not found.</p>";
    }
}

// Update the item
if (isset($_POST['update_item'])) {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author_name = $_POST['author_name'];
    $category = $_POST['category'];
    $cost = $_POST['cost'];
    $procurement_date = $_POST['procurement_date'];
    $status = $_POST['status'];

    $sql = "UPDATE books SET title = ?, author_name = ?, category = ?, cost = ?, procurement_date = ?, status = ? WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdssi", $title, $author_name, $category, $cost, $procurement_date, $status, $book_id);

    if ($stmt->execute()) {
        $message = "<p style='color: green;'>Item details updated successfully!</p>";
        $item_details = null; // Clear the form after successful update
    } else {
        $message = "<p style='color: red;'>Error updating item details: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Remove the item
if (isset($_POST['remove_item'])) {
    $book_id = $_POST['book_id'];
    $sql = "DELETE FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);

    if ($stmt->execute()) {
        $message = "<p style='color: green;'>Item removed successfully!</p>";
        $item_details = null; // Clear the form after removal
    } else {
        $message = "<p style='color: red;'>Error removing item: " . $stmt->error . "</p>";
    }
    $stmt->close();
}
?>

<div class="container">
    <h2>Update or Remove Book/Movie</h2>
    <?php echo $message; ?>
    
    <form action="" method="post">
        <p>
            <label>Search by Serial No:</label>
            <input type="text" name="serial_no" required>
        </p>
        <button type="submit" name="search_item">Search</button>
    </form>
    
    <?php if ($item_details) { ?>
        <hr>
        <h3>Details for: <?php echo htmlspecialchars($item_details['serial_no']); ?></h3>
        
        <form action="" method="post">
            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($item_details['book_id']); ?>">
            <p>
                <label>Title:</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($item_details['title']); ?>" required>
            </p>
            <p>
                <label>Author/Director:</label>
                <input type="text" name="author_name" value="<?php echo htmlspecialchars($item_details['author_name']); ?>" required>
            </p>
            <p>
                <label>Category:</label>
                <input type="text" name="category" value="<?php echo htmlspecialchars($item_details['category']); ?>" required>
            </p>
            <p>
                <label>Cost:</label>
                <input type="number" name="cost" step="0.01" value="<?php echo htmlspecialchars($item_details['cost']); ?>" required>
            </p>
            <p>
                <label>Procurement Date:</label>
                <input type="date" name="procurement_date" value="<?php echo htmlspecialchars($item_details['procurement_date']); ?>" required>
            </p>
            <p>
                <label>Status:</label>
                <select name="status">
                    <option value="Available" <?php if ($item_details['status'] == 'Available') echo 'selected'; ?>>Available</option>
                    <option value="Issued" <?php if ($item_details['status'] == 'Issued') echo 'selected'; ?>>Issued</option>
                    <option value="Lost" <?php if ($item_details['status'] == 'Lost') echo 'selected'; ?>>Lost</option>
                </select>
            </p>
            
            <button type="submit" name="update_item">Update Item</button>
            <button type="submit" name="remove_item" onclick="return confirm('Are you sure you want to remove this item?');">Remove Item</button>
        </form>
    <?php } ?>
</div>

<?php include("../includes/footer.php"); ?>