<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

// Check if the user is an admin; if not, redirect
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';

if (isset($_POST['issue_book'])) {
    $member_id = $_POST['member_id'];
    $book_id = $_POST['book_id'];
    $issue_date = $_POST['issue_date'];
    $return_date = $_POST['return_date'];
    $remarks = $_POST['remarks'];

    // Check if the book is available
    $check_sql = "SELECT available FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $check_result = $stmt->get_result();
    $book_data = $check_result->fetch_assoc();
    $stmt->close();

    if ($book_data && $book_data['available'] > 0) {
        // Start a transaction for atomicity
        $conn->begin_transaction();
        try {
            // Insert into issue_records
            $issue_sql = "INSERT INTO issue_records (member_id, book_id, issue_date, return_date, remarks, status) VALUES (?, ?, ?, ?, ?, 'issued')";
            $issue_stmt = $conn->prepare($issue_sql);
            $issue_stmt->bind_param("iiss", $member_id, $book_id, $issue_date, $return_date, $remarks);
            $issue_stmt->execute();

            // Decrement the available count for the book
            $update_sql = "UPDATE books SET available = available - 1 WHERE book_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $book_id);
            $update_stmt->execute();

            $conn->commit();
            $message = "<p style='color: green;'>Book issued successfully!</p>";
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            $message = "<p style='color: red;'>Transaction failed: " . $e->getMessage() . "</p>";
        } finally {
            if (isset($issue_stmt)) $issue_stmt->close();
            if (isset($update_stmt)) $update_stmt->close();
        }
    } else {
        $message = "<p style='color: red;'>This item is not available for issue at the moment.</p>";
    }
}
?>

<div class="container">
    <h2>Issue Book/Movie</h2>
    <?php echo $message; ?>

    <form action="" method="post">
        <p>
            <label>Member ID:</label>
            <input type="text" name="member_id" required>
        </p>
        <p>
            <label>Book/Movie ID:</label>
            <input type="text" name="book_id" required>
        </p>
        <p>
            <label>Issue Date:</label>
            <input type="date" name="issue_date" value="<?php echo date('Y-m-d'); ?>" required>
        </p>
        <p>
            <label>Return Date:</label>
            <input type="date" name="return_date" value="<?php echo date('Y-m-d', strtotime('+15 days')); ?>" required>
        </p>
        <p>
            <label>Remarks (optional):</label>
            <textarea name="remarks"></textarea>
        </p>
        <button type="submit" name="issue_book">Issue Book</button>
    </form>
</div>

<?php include("../includes/footer.php"); ?>