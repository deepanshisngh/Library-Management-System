<?php
session_start();
include("../config/db.php");
include("../includes/header.php");

// Your existing login logic
if (isset($_POST['login'])) {
    // ... (Your login logic remains the same)
}
?>

<div class="container">
    <h2>Login</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>

<?php
include("../includes/footer.php");
?>