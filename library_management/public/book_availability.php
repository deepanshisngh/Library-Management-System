<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

// Check if a user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';
$search_results = [];

// Handle search for books or movies
if (isset($_POST['search'])) {
    $search_term = '%' . $_POST['search_term'] . '%';
    
    // Using prepared statement for security
    $sql = "SELECT serial_no, title, author, category, status FROM books WHERE title LIKE ? OR author LIKE ? OR category LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $search_results[] = $row;
        }
    } else {
        $message = "<p style='color: red;'>No results found.</p>";
    }
    $stmt->close();
}
?>

<div class="container">
    <h2>Book/Movie Availability</h2>
    
    <form action="" method="post">
        <p>
            <label>Search by Title, Author, or Category:</label>
            <input type="text" name="search_term" required>
        </p>
        <button type="submit" name="search">Search</button>
    </form>
    
    <?php if (!empty($search_results)) { ?>
        <hr>
        <h3>Search Results</h3>
        <table>
            <thead>
                <tr>
                    <th>Serial No</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($search_results as $book) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['serial_no']); ?></td>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo htmlspecialchars($book['category']); ?></td>
                    <td><?php echo htmlspecialchars($book['status']); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else {
        echo $message;
    } ?>
</div>

<?php include("../includes/footer.php"); ?>