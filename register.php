<?php

include "database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (FirstName, LastName, Email, Password, Role)
            VALUES (?, ?, ?, ?, ?)";

    $statement = $connection->prepare($sql);

    $role = "customer";

    $statement->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $role);

    if ($statement->execute()) {
        $message = "Account created successfully! You can now log in.";
    } else {
        $message = "Error creating account. The email may already be in use.";
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

    <?php if ($message != "") { ?>
        <p><?php echo $message; ?></p>
    <?php } ?>

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
