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
$user_details = null;

// Handle new user creation
if (isset($_POST['create_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $password, $role);
    
    if ($stmt->execute()) {
        $message = "<p style='color: green;'>New user created successfully!</p>";
    } else {
        $message = "<p style='color: red;'>Error creating user: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Handle search for existing user
if (isset($_POST['search_user'])) {
    $search_username = $_POST['search_username'];
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search_username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_details = $result->fetch_assoc();
    $stmt->close();

    if (!$user_details) {
        $message = "<p style='color: red;'>User not found.</p>";
    }
}

// Handle update for existing user
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET username = ?, role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $role, $user_id);
    
    if ($stmt->execute()) {
        $message = "<p style='color: green;'>User details updated successfully!</p>";
        $user_details = null; // Clear the form
    } else {
        $message = "<p style='color: red;'>Error updating user: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

?>

<div class="container">
    <h2>User Management</h2>
    <?php echo $message; ?>

    <h3>Create New User</h3>
    <form action="" method="post">
        <p>
            <label>Username:</label>
            <input type="text" name="username" required>
        </p>
        <p>
            <label>Password:</label>
            <input type="password" name="password" required>
        </p>
        <p>
            <label>Role:</label>
            <select name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </p>
        <button type="submit" name="create_user">Create User</button>
    </form>
    
    <hr>

    <h3>Update Existing User</h3>
    <form action="" method="post">
        <p>
            <label>Search by Username:</label>
            <input type="text" name="search_username" required>
        </p>
        <button type="submit" name="search_user">Search</button>
    </form>

    <?php if ($user_details) { ?>
        <form action="" method="post">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_details['user_id']); ?>">
            <p>
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user_details['username']); ?>" required>
            </p>
            <p>
                <label>Role:</label>
                <select name="role">
                    <option value="user" <?php if ($user_details['role'] == 'user') echo 'selected'; ?>>User</option>
                    <option value="admin" <?php if ($user_details['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                </select>
            </p>
            <button type="submit" name="update_user">Update User</button>
        </form>
    <?php } ?>
</div>

<?php include("../includes/footer.php"); ?>