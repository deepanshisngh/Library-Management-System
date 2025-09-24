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

if (isset($_POST['return_book'])) {
    $serial_no = $_POST['serial_no'];
    $return_date = date("Y-m-d");

    // Start a transaction for atomicity
    $conn->begin_transaction();
    try {
        // Find the active issue record for the given serial number
        $sql = "SELECT i.issue_id, i.member_id, i.due_date, b.book_id
                FROM issue_records i
                JOIN books b ON i.book_id = b.book_id
                WHERE b.serial_no = ? AND i.status = 'issued'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $serial_no);
        $stmt->execute();
        $result = $stmt->get_result();
        $issue_data = $result->fetch_assoc();
        $stmt->close();

        if ($issue_data) {
            $fine_per_day = 10; // Assuming a fine of Rs. 10 per day
            $fine_amount = 0;
            $overdue_days = 0;

            // Calculate overdue days and fine
            $due_date = new DateTime($issue_data['due_date']);
            $current_date = new DateTime($return_date);

            if ($current_date > $due_date) {
                $interval = $current_date->diff($due_date);
                $overdue_days = $interval->days;
                $fine_amount = $overdue_days * $fine_per_day;

                // Update fine_due in the members table
                $update_fine_sql = "UPDATE members SET fine_due = fine_due + ? WHERE member_id = ?";
                $update_fine_stmt = $conn->prepare($update_fine_sql);
                $update_fine_stmt->bind_param("di", $fine_amount, $issue_data['member_id']);
                $update_fine_stmt->execute();
                $update_fine_stmt->close();
            }

            // Update the issue record
            $update_issue_sql = "UPDATE issue_records SET status = 'returned', return_date = ?, fine_amount = ? WHERE issue_id = ?";
            $update_issue_stmt = $conn->prepare($update_issue_sql);
            $update_issue_stmt->bind_param("sdi", $return_date, $fine_amount, $issue_data['issue_id']);
            $update_issue_stmt->execute();
            $update_issue_stmt->close();

            // Increment the available count for the book
            $update_book_sql = "UPDATE books SET available = available + 1 WHERE book_id = ?";
            $update_book_stmt = $conn->prepare($update_book_sql);
            $update_book_stmt->bind_param("i", $issue_data['book_id']);
            $update_book_stmt->execute();
            $update_book_stmt->close();

            $conn->commit();
            $message = "<p style='color: green;'>Item returned successfully! Overdue days: $overdue_days. Fine incurred: Rs. " . number_format($fine_amount, 2) . ".</p>";
        } else {
            $message = "<p style='color: red;'>No active issue record found for this Serial No.</p>";
            $conn->rollback();
        }
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $message = "<p style='color: red;'>Transaction failed: " . $e->getMessage() . "</p>";
    }
}
?>

<div class="container">
    <h2>Return Book/Movie</h2>
    <?php echo $message; ?>

    <form action="" method="post">
        <p>
            <label>Serial No. of Item:</label>
            <input type="text" name="serial_no" required>
        </p>
        <button type="submit" name="return_book">Return Item</button>
    </form>
</div>

<?php include("../includes/footer.php"); ?>