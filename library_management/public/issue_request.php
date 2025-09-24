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

if (isset($_POST['request_book'])) {
    $book_serial_no = $_POST['book_serial_no'];
    $request_date = date("Y-m-d");
    $member_id = $_SESSION['user_id'];

    // First, check if the book exists and is not available
    $sql_book = "SELECT book_id, status FROM books WHERE serial_no = ?";
    $stmt_book = $conn->prepare($sql_book);
    $stmt_book->bind_param("s", $book_serial_no);
    $stmt_book->execute();
    $result_book = $stmt_book->get_result();
    $book = $result_book->fetch_assoc();
    $stmt_book->close();

    if (!$book) {
        $message = "<p style='color: red;'>Book/Movie not found.</p>";
    } elseif ($book['status'] === 'Available') {
        $message = "<p style='color: green;'>This item is already available! You can visit the library to get it.</p>";
    } else {
        // Check if the user has already requested this book
        $sql_check = "SELECT * FROM issue_requests WHERE member_id = ? AND book_id = ? AND status = 'Pending'";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ii", $member_id, $book['book_id']);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $stmt_check->close();

        if ($result_check->num_rows > 0) {
            $message = "<p style='color: red;'>You have already submitted a request for this item.</p>";
        } else {
            // Insert the request into the issue_requests table
            $sql_insert = "INSERT INTO issue_requests (member_id, book_id, request_date, status) VALUES (?, ?, ?, 'Pending')";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iis", $member_id, $book['book_id'], $request_date);
            
            if ($stmt_insert->execute()) {
                $message = "<p style='color: green;'>Your request has been submitted successfully!</p>";
            } else {
                $message = "<p style='color: red;'>Error submitting request: " . $stmt_insert->error . "</p>";
            }
            $stmt_insert->close();
        }
    }
}
?>

<div class="container">
    <h2>Request a Book or Movie</h2>
    <?php echo $message; ?>
    
    <form action="" method="post">
        <p>
            <label>Serial No of Book/Movie:</label>
            <input type="text" name="book_serial_no" required>
        </p>
        
        <button type="submit" name="request_book">Submit Request</button>
    </form>
</div>

<?php include("../includes/footer.php"); ?>