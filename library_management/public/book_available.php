<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

$message = '';
$results = [];

if (isset($_POST['search'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];

    if (empty($title) && empty($author)) {
        $message = "<p style='color: red;'>Please enter either a Book Name or an Author to search.</p>";
    } else {
        $sql = "SELECT * FROM books WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($title)) {
            $sql .= " AND title LIKE ?";
            $types .= 's';
            $params[] = "%" . $title . "%";
        }
        if (!empty($author)) {
            $sql .= " AND author LIKE ?";
            $types .= 's';
            $params[] = "%" . $author . "%";
        }
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            if ($types) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            $stmt->close();
        } else {
            $message = "<p style='color: red;'>Database error: " . $conn->error . "</p>";
        }
    }
}
?>

<div class="container">
    <h2>Book Availability</h2>
    <form action="" method="post">
        <p><input type="text" name="title" placeholder="Enter Book/Movie Name"></p>
        <p><input type="text" name="author" placeholder="Enter Author"></p>
        <button type="submit" name="search">Search</button>
    </form>
    
    <?php echo $message; ?>
    
    <?php if (!empty($results)) { ?>
        <hr>
        <h3>Search Results</h3>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Serial No</th>
                    <th>Available</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['author']); ?></td>
                        <td><?php echo htmlspecialchars($row['serial_no']); ?></td>
                        <td><?php echo ($row['available'] > 0) ? 'Yes' : 'No'; ?></td>
                        <td>
                            <?php if ($row['available'] > 0) { ?>
                                <input type="radio" name="select_book" value="<?php echo $row['book_id']; ?>">
                                <label>Select to issue</label>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>

<?php include("../includes/footer.php"); ?>