<?php

session_start();

if (!isset($_SESSION["UserID"])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Account - Carolina Pawn & Trade</title>
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

<div class="account-container">

    <h1>My Account</h1>

    <div class="account-box">

        <h2>Welcome, <?php echo $_SESSION["FirstName"]; ?>!</h2>

        <p><strong>First Name:</strong> <?php echo $_SESSION["FirstName"]; ?></p>

        <p><strong>Last Name:</strong> <?php echo $_SESSION["LastName"]; ?></p>

        <p><strong>Email:</strong> <?php echo $_SESSION["Email"]; ?></p>

        <p><strong>Account Type:</strong> <?php echo $_SESSION["Role"]; ?></p>

    </div>

    <div class="account-box">

        <h2>Account Options</h2>

        <button>Change Password</button>

        <br><br>

        <a href="logout.php">Logout</a>

    </div>

</div>

</body>
</html>
