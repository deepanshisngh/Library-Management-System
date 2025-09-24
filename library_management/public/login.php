<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

$message = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT user_id, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 1) {
        // More than one user with the same username (security issue)
        die("Error: Duplicate username found.");
    }

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Debugging line - REMOVE THIS LATER
        echo "Comparing entered password '" . htmlspecialchars($password) . "' with database hash '" . htmlspecialchars($user['password']) . "'<br>";

        // Use password_verify to check hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
                exit();
            } elseif ($user['role'] == 'user') {
                header("Location: user_dashboard.php");
                exit();
            }
        } else {
            $message = "<p style='color: red;'>Incorrect username or password.</p>";
        }
    } else {
        $message = "<p style='color: red;'>Incorrect username or password.</p>";
    }
    $stmt->close();
}
?>

<div class="container">
    <h2>Login</h2>
    <?php echo $message; ?>
    <form action="" method="post">
        <p>
            <label>Username:</label>
            <input type="text" name="username" required>
        </p>
        <p>
            <label>Password:</label>
            <input type="password" name="password" required>
        </p>
        <button type="submit" name="login">Login</button>
    </form>
</div>

<?php include("../includes/footer.php"); ?>