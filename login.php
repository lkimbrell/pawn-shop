<?php

session_start();

include "database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE Email = ?";

    $statement = $connection->prepare($sql);
    $statement->bind_param("s", $email);
    $statement->execute();

    $result = $statement->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["Password"])) {

            $_SESSION["UserID"] = $user["UserID"];
            $_SESSION["FirstName"] = $user["FirstName"];
            $_SESSION["LastName"] = $user["LastName"];
            $_SESSION["Email"] = $user["Email"];
            $_SESSION["Role"] = $user["Role"];

            header("Location: account.php");
            exit();

        } else {
            $error = "Invalid email or password.";
        }

    } else {
        $error = "Invalid email or password.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Carolina Pawn & Trade</title>
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

    <h1>Login</h1>

    <?php if ($error != "") { ?>
        <p><?php echo $error; ?></p>
    <?php } ?>

    <form method="POST" action="login.php">

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>

    </form>

    <br>

    <a href="register.php">Create an Account</a>

</div>

</body>
</html>
