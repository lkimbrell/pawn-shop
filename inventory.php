```php
<?php

// Connect to the database
include "database.php";

// Get the search value
$search = "";

if (isset($_GET["search"])) {
    $search = $_GET["search"];
}

// Search for products if a search was entered
if ($search != "") {

    // Add wildcards to allow partial searches
    $search = "%" . $search . "%";

    // Search by product name or category
    $sql = "SELECT products.*, categories.CategoryName
            FROM products
            LEFT JOIN categories
            ON products.CategoryID = categories.CategoryID
            WHERE products.ProductName LIKE ?
            OR categories.CategoryName LIKE ?";

    // Prepare the search
    $statement = $connection->prepare($sql);
    $statement->bind_param("ss", $search, $search);
    $statement->execute();

    // Get the search results
    $result = $statement->get_result();

} else {

    // Display all products when no search is entered
    $sql = "SELECT products.*, categories.CategoryName
            FROM products
            LEFT JOIN categories
            ON products.CategoryID = categories.CategoryID";

    $result = $connection->query($sql);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory - Carolina Pawn & Trade</title>
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

<div class="inventory-container">

    <h1>Inventory</h1>

    <!-- Search instructions -->
    <p>Search for an item by name or category.</p>

    <!-- Search form -->
    <form method="GET" action="inventory.php">

        <input
            type="text"
            name="search"
            placeholder="Search inventory..."
            value="<?php echo htmlspecialchars($_GET["search"] ?? ""); ?>"
        >

        <button type="submit">Search</button>

    </form>

    <br>

    <!-- Display products -->
    <?php if ($result->num_rows > 0) { ?>

        <?php while ($product = $result->fetch_assoc()) { ?>

            <div class="product">

                <h2><?php echo htmlspecialchars($product["ProductName"]); ?></h2>

                <p>
                    <strong>Category:</strong>
                    <?php echo htmlspecialchars($product["CategoryName"]); ?>
                </p>

                <p>
                    <strong>Description:</strong>
                    <?php echo htmlspecialchars($product["Description"]); ?>
                </p>

                <p>
                    <strong>Condition:</strong>
                    <?php echo htmlspecialchars($product["ProductCondition"]); ?>
                </p>

                <p>
                    <strong>Price:</strong>
                    $<?php echo number_format($product["Price"], 2); ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    <?php echo htmlspecialchars($product["Status"]); ?>
                </p>

            </div>

            <hr>

        <?php } ?>

    <?php } else { ?>

        <!-- Display message when no products are found -->
        <p>No products were found.</p>

    <?php } ?>

</div>

</body>
</html>
```
