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

if (isset($_POST['issue_book'])) {
    $membership_id = $_POST['membership_id'];
    $serial_no = $_POST['serial_no'];
    $issue_date = date("Y-m-d");
    $return_date = date('Y-m-d', strtotime($issue_date . ' + 15 days')); // Default 15-day return period

    // Check if the book and user exist and the book is available
    $sql_book = "SELECT book_id, status FROM books WHERE serial_no = ?";
    $stmt_book = $conn->prepare($sql_book);
    $stmt_book->bind_param("s", $serial_no);
    $stmt_book->execute();
    $result_book = $stmt_book->get_result();
    $book = $result_book->fetch_assoc();
    $stmt_book->close();

    $sql_member = "SELECT member_id FROM members WHERE membership_id = ?";
    $stmt_member = $conn->prepare($sql_member);
    $stmt_member->bind_param("s", $membership_id);
    $stmt_member->execute();
    $result_member = $stmt_member->get_result();
    $member = $result_member->fetch_assoc();
    $stmt_member->close();

    if (!$book || $book['status'] != 'Available') {
        $message = "<p style='color: red;'>Book/Movie not found or not available.</p>";
    } elseif (!$member) {
        $message = "<p style='color: red;'>Membership ID not found.</p>";
    } else {
        // Insert a new issue record
        $sql_issue = "INSERT INTO issue_records (member_id, book_id, issue_date, due_date) VALUES (?, ?, ?, ?)";
        $stmt_issue = $conn->prepare($sql_issue);
        $stmt_issue->bind_param("iiss", $member['member_id'], $book['book_id'], $issue_date, $return_date);
        
        if ($stmt_issue->execute()) {
            // Update the book's status
            $sql_update_book = "UPDATE books SET status = 'Not Available' WHERE book_id = ?";
            $stmt_update_book = $conn->prepare($sql_update_book);
            $stmt_update_book->bind_param("i", $book['book_id']);
            $stmt_update_book->execute();
            $stmt_update_book->close();

            $message = "<p style='color: green;'>Book/Movie issued successfully!</p>";
        } else {
            $message = "<p style='color: red;'>Error issuing book/movie: " . $stmt_issue->error . "</p>";
        }
        $stmt_issue->close();
    }
}
?>

<div class="container">
    <h2>Issue a Book or Movie</h2>
    <?php echo $message; ?>
    
    <form action="" method="post">
        <p>
            <label>Membership ID:</label>
            <input type="text" name="membership_id" required>
        </p>
        <p>
            <label>Serial No of Book/Movie:</label>
            <input type="text" name="serial_no" required>
        </p>
        
        <button type="submit" name="issue_book">Issue Book/Movie</button>
    </form>
</div>

<?php include("../includes/footer.php"); ?>