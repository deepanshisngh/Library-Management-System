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
$member_details = null;

if (isset($_POST['search_member'])) {
    $membership_id = $_POST['membership_id'];
    $sql = "SELECT member_id, first_name, last_name, fine_due FROM members WHERE membership_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $membership_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $member_details = $result->fetch_assoc();
    $stmt->close();

    if (!$member_details) {
        $message = "<p style='color: red;'>Membership ID not found.</p>";
    }
}

if (isset($_POST['pay_fine'])) {
    $member_id = $_POST['member_id'];
    $amount_paid = $_POST['amount_paid'];

    // Ensure the amount paid is a positive number
    if ($amount_paid <= 0) {
        $message = "<p style='color: red;'>Amount paid must be a positive number.</p>";
    } else {
        // Update the member's fine_due
        $sql = "UPDATE members SET fine_due = fine_due - ? WHERE member_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $amount_paid, $member_id);

        if ($stmt->execute()) {
            $message = "<p style='color: green;'>Fine of Rs. " . number_format($amount_paid, 2) . " paid successfully!</p>";
            // Reset the form by clearing member details
            $member_details = null; 
        } else {
            $message = "<p style='color: red;'>Error processing payment.</p>";
        }
        $stmt->close();
    }
}
?>

<div class="container">
    <h2>Pay Fine</h2>
    <?php echo $message; ?>
    
    <form action="" method="post">
        <p>
            <label>Search Membership ID:</label>
            <input type="text" name="membership_id" required>
        </p>
        <button type="submit" name="search_member">Search</button>
    </form>
    
    <?php if ($member_details) { ?>
        <hr>
        <h3>Member Details</h3>
        <p>Name: <?php echo htmlspecialchars($member_details['first_name'] . ' ' . $member_details['last_name']); ?></p>
        <p>Current Outstanding Fine: **Rs. <?php echo number_format($member_details['fine_due'], 2); ?>**</p>
        
        <form action="" method="post">
            <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($member_details['member_id']); ?>">
            <p>
                <label>Amount to Pay:</label>
                <input type="number" name="amount_paid" step="0.01" min="0.01" required>
            </p>
            <button type="submit" name="pay_fine">Submit Payment</button>
        </form>
    <?php } ?>
</div>

<?php include("../includes/footer.php"); ?>