```php
<?php

// Connect to the database
include "database.php";

$message = "";

// Check if the registration form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the information from the form
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Check if the password is at least 8 characters
    if (strlen($password) < 8) {

        $message = "Password must be at least 8 characters.";

    // Check if the password contains a number
    } elseif (!preg_match("/[0-9]/", $password)) {

        $message = "Password must contain at least one number.";

    // Check if the password contains a special character
    } elseif (!preg_match("/[^a-zA-Z0-9]/", $password)) {

        $message = "Password must contain at least one special character.";

    } else {

        // Securely hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Add the new user to the database
        $sql = "INSERT INTO users (FirstName, LastName, Email, Password, Role)
                VALUES (?, ?, ?, ?, ?)";

        $statement = $connection->prepare($sql);

        // Set the new user as a customer
        $role = "customer";

        $statement->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $role);

        // Check if the account was created
        if ($statement->execute()) {

            $message = "Account created successfully! You can now log in.";

        } else {

            $message = "Error creating account. The email may already be in use.";

        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Account - Carolina Pawn & Trade</title>
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

    <h1>Create Account</h1>

    <!-- Password requirements -->
    <p>
        Password must be at least 8 characters and contain
        one number and one special character.
    </p>

    <!-- Display registration message -->
    <?php if ($message != "") { ?>
        <p><?php echo $message; ?></p>
    <?php } ?>

    <!-- Registration form -->
    <form method="POST" action="register.php">

        <label>First Name:</label>
        <input type="text" name="firstName" required>

        <label>Last Name:</label>
        <input type="text" name="lastName" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Create Account</button>

    </form>

    <br>

    <a href="login.php">Already have an account? Login</a>

</div>

</body>
</html>
```
