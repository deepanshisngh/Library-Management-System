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

if (isset($_POST['add_member'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $contact_name = $_POST['contact_name'];
    $contact_address = $_POST['contact_address'];
    $aadhar_card_no = $_POST['aadhar_card_no'];
    $membership_type = $_POST['membership_type'];
    $start_date = date("Y-m-d");

    // Calculate end date based on membership type
    if ($membership_type == '6_months') {
        $end_date = date('Y-m-d', strtotime($start_date . ' + 6 months'));
    } elseif ($membership_type == '1_year') {
        $end_date = date('Y-m-d', strtotime($start_date . ' + 1 year'));
    } else { // 2 years
        $end_date = date('Y-m-d', strtotime($start_date . ' + 2 years'));
    }

    // Generate a unique membership ID
    $membership_id = 'M' . uniqid();

    // Insert new member record
    $sql = "INSERT INTO members (membership_id, first_name, last_name, contact_name, contact_address, aadhar_card_no, start_date, end_date, membership_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $membership_id, $first_name, $last_name, $contact_name, $contact_address, $aadhar_card_no, $start_date, $end_date, $membership_type);

    if ($stmt->execute()) {
        $message = "<p style='color: green;'>Member added successfully! Membership ID: " . htmlspecialchars($membership_id) . "</p>";
    } else {
        $message = "<p style='color: red;'>Error adding member: " . $stmt->error . "</p>";
    }
    $stmt->close();
}
?>

<div class="container">
    <h2>Add New Membership</h2>
    <?php echo $message; ?>
    
    <form action="" method="post">
        <p>
            <label>First Name:</label>
            <input type="text" name="first_name" required>
        </p>
        <p>
            <label>Last Name:</label>
            <input type="text" name="last_name" required>
        </p>
        <p>
            <label>Contact Name:</label>
            <input type="text" name="contact_name" required>
        </p>
        <p>
            <label>Contact Address:</label>
            <input type="text" name="contact_address" required>
        </p>
        <p>
            <label>Aadhar Card No:</label>
            <input type="text" name="aadhar_card_no" required>
        </p>
        <p>
            <label>Membership Duration:</label><br>
            <input type="radio" name="membership_type" value="6_months" checked> 6 Months<br>
            <input type="radio" name="membership_type" value="1_year"> 1 Year<br>
            <input type="radio" name="membership_type" value="2_years"> 2 Years
        </p>
        
        <button type="submit" name="add_member">Add Member</button>
    </form>
</div>

<?php include("../includes/footer.php"); ?>