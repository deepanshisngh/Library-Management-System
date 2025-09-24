<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

$message = '';
$search_results = null;

if (isset($_GET['search'])) {
    $search_query = $_GET['search_query'];
    
    // Prevent SQL Injection using prepared statements
    $sql = "SELECT * FROM books WHERE title LIKE ? OR author_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $search_results = $stmt->get_result();
    $stmt->close();
}
?>

<div class="container">
    <h2>Search Books & Movies</h2>
    <p>Find your favorite books and movies by title or author.</p>
    
    <form action="" method="get">
        <p>
            <label>Search:</label>
            <input type="text" name="search_query" required>
            <button type="submit" name="search">Search</button>
        </p>
    </form>
    
    <hr>
    
    <?php if ($search_results) { ?>
        <?php if ($search_results->num_rows > 0) { ?>
            <h3>Search Results</h3>
            <table border="1">
                <tr>
                    <th>Serial No.</th>
                    <th>Title</th>
                    <th>Author/Director</th>
                    <th>Category</th>
                    <th>Status</th>
                </tr>
                <?php while ($row = $search_results->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['serial_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['author_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No results found for your search query.</p>
        <?php } ?>
    <?php } ?>
    
    <?php echo $message; ?>
</div>

<?php include("../includes/footer.php"); ?>