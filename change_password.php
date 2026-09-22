```php
<?php

// Start the user session
session_start();

// Connect to the database
include "database.php";

// Send the user to login if they are not logged in
if (!isset($_SESSION["UserID"])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the passwords from the form
    $currentPassword = $_POST["currentPassword"];
    $newPassword = $_POST["newPassword"];

    // Get the current user's ID
    $userID = $_SESSION["UserID"];

    // Get the current password from the database
    $sql = "SELECT Password FROM users WHERE UserID = ?";

    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $userID);
    $statement->execute();

    $result = $statement->get_result();

    $user = $result->fetch_assoc();

    // Check the current password
    if (!password_verify($currentPassword, $user["Password"])) {

        $message = "Current password is incorrect.";

    // Check if the new password is at least 8 characters
    } elseif (strlen($newPassword) < 8) {

        $message = "Password must be at least 8 characters.";

    // Check if the new password contains a number
    } elseif (!preg_match("/[0-9]/", $newPassword)) {

        $message = "Password must contain at least one number.";

    // Check if the new password contains a special character
    } elseif (!preg_match("/[^a-zA-Z0-9]/", $newPassword)) {

        $message = "Password must contain at least one special character.";

    } else {

        // Securely hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $sql = "UPDATE users SET Password = ? WHERE UserID = ?";

        $statement = $connection->prepare($sql);
        $statement->bind_param("si", $hashedPassword, $userID);

        // Check if the password was changed
        if ($statement->execute()) {

            $message = "Password changed successfully.";

        } else {

            $message = "There was an error changing your password.";

        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password - Carolina Pawn & Trade</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<!-- Website navigation -->
<nav>
    <h2>Carolina Pawn & Trade</h2>
    <a href="index.php">Home</a>
    <a href="inventory.php">Inventory</a>
    <a href="about.php">About</a>
    <a href="login.php">Login</a>
    <a href="account.php">Account</a>
</nav>

<div class="login-container">

    <h1>Change Password</h1>

    <!-- Password requirements -->
    <p>
        Password must be at least 8 characters and contain
        one number and one special character.
    </p>

    <!-- Display password message -->
    <?php if ($message != "") { ?>
        <p><?php echo $message; ?></p>
    <?php } ?>

    <!-- Change password form -->
    <form method="POST" action="change_password.php">

        <label>Current Password:</label>
        <input type="password" name="currentPassword" required>

        <label>New Password:</label>
        <input type="password" name="newPassword" required>

        <button type="submit">Change Password</button>

    </form>

    <br>

    <a href="account.php">Back to Account</a>

</div>

</body>
</html>
```
