<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$message = '';
$membership_data = [];

// Handle search for an existing membership
if (isset($_POST['search_membership'])) {
    $membership_id = $_POST['membership_id'];
    
    $sql = "SELECT * FROM memberships WHERE membership_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $membership_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $membership_data = $result->fetch_assoc();
    } else {
        $message = "<p style='color: red;'>No membership found with that ID.</p>";
    }
    $stmt->close();
}

// Handle update or removal of membership
if (isset($_POST['update_membership'])) {
    $membership_id = $_POST['membership_id'];
    $action = $_POST['action'];

    if ($action == 'remove') {
        $sql = "UPDATE memberships SET status='Inactive', end_date=CURDATE() WHERE membership_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $membership_id);
        
        if ($stmt->execute()) {
            $message = "<p style='color: green;'>Membership removed successfully!</p>";
        } else {
            $message = "<p style='color: red;'>Error removing membership: " . $stmt->error . "</p>";
        }
    } else { // This handles the extensions
        $current_end_date = $_POST['current_end_date'];
        $extension_months = $_POST['extension_type'];
        
        $new_end_date = new DateTime($current_end_date);
        $new_end_date->modify('+' . $extension_months . ' months');
        $new_end_date_str = $new_end_date->format('Y-m-d');
        
        $sql = "UPDATE memberships SET end_date=? WHERE membership_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_end_date_str, $membership_id);
        
        if ($stmt->execute()) {
            $message = "<p style='color: green;'>Membership extended successfully!</p>";
        } else {
            $message = "<p style='color: red;'>Error extending membership: " . $stmt->error . "</p>";
        }
    }
    $stmt->close();
}
?>

<div class="container">
    <h2>Update/Remove Membership</h2>
    <?php echo $message; ?>
    
    <form action="" method="post">
        <p><input type="text" name="membership_id" placeholder="Enter Membership ID" required></p>
        <button type="submit" name="search_membership">Search</button>
    </form>
    
    <?php if (!empty($membership_data)) { ?>
        <hr>
        <h3>Membership Details</h3>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($membership_data['first_name'] . ' ' . $membership_data['last_name']); ?></p>
        <p><strong>Current End Date:</strong> <?php echo htmlspecialchars($membership_data['end_date']); ?></p>

        <form action="" method="post">
            <input type="hidden" name="membership_id" value="<?php echo htmlspecialchars($membership_data['membership_id']); ?>">
            <input type="hidden" name="current_end_date" value="<?php echo htmlspecialchars($membership_data['end_date']); ?>">
            
            <p><strong>Action:</strong></p>
            <p>
                <input type="radio" name="action" value="extend" id="extend" checked>
                <label for="extend">Extend Membership</label>
            </p>
            <div id="extension_options">
                <p>
                    <input type="radio" name="extension_type" value="6" checked> Six Months
                    <input type="radio" name="extension_type" value="12"> One Year
                    <input type="radio" name="extension_type" value="24"> Two Years
                </p>
            </div>
            <p>
                <input type="radio" name="action" value="remove" id="remove">
                <label for="remove">Remove Membership</label>
            </p>
            
            <button type="submit" name="update_membership">Confirm Action</button>
        </form>
    <?php } ?>
</div>

<?php include("../includes/footer.php"); ?>