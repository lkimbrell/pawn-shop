<?php

// Start the user session
session_start();

// Connect to the database
include "database.php";

// Check if the user is an admin
$isAdmin = false;

if (isset($_SESSION["Role"]) && $_SESSION["Role"] == "admin") {
    $isAdmin = true;
}

$message = "";

// Add a product if the user is an admin
if (isset($_POST["add_product"]) && $isAdmin) {

    $productName = $_POST["productName"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $categoryID = $_POST["categoryID"];
    $condition = $_POST["condition"];

    // Add the product to the database
    $sql = "INSERT INTO products
            (ProductName, Description, Price, CategoryID, ProductCondition, Status)
            VALUES (?, ?, ?, ?, ?, 'Available')";

    $statement = $connection->prepare($sql);
    $statement->bind_param(
        "ssdis",
        $productName,
        $description,
        $price,
        $categoryID,
        $condition
    );

    if ($statement->execute()) {
        $message = "Product added successfully.";
    } else {
        $message = "Error adding product.";
    }
}

// Delete a product if the user is an admin
if (isset($_POST["delete_product"]) && $isAdmin) {

    $productID = $_POST["productID"];

    // Delete the product from the database
    $sql = "DELETE FROM products WHERE ProductID = ?";

    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $productID);

    if ($statement->execute()) {
        $message = "Product deleted successfully.";
    } else {
        $message = "Error deleting product.";
    }
}

// Edit a product if the user is an admin
if (isset($_POST["edit_product"]) && $isAdmin) {

    $productID = $_POST["productID"];
    $productName = $_POST["productName"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $categoryID = $_POST["categoryID"];
    $condition = $_POST["condition"];

    // Update the product in the database
    $sql = "UPDATE products
            SET ProductName = ?,
                Description = ?,
                Price = ?,
                CategoryID = ?,
                ProductCondition = ?
            WHERE ProductID = ?";

    $statement = $connection->prepare($sql);
    $statement->bind_param(
        "ssdisi",
        $productName,
        $description,
        $price,
        $categoryID,
        $condition,
        $productID
    );

    if ($statement->execute()) {
        $message = "Product updated successfully.";
    } else {
        $message = "Error updating product.";
    }
}

// Get the search value
$search = "";

if (isset($_GET["search"])) {
    $search = $_GET["search"];
}

// Search for products
if ($search != "") {

    // Add wildcards to allow partial searches
    $searchValue = "%" . $search . "%";

    $sql = "SELECT products.*, categories.CategoryName
            FROM products
            LEFT JOIN categories
            ON products.CategoryID = categories.CategoryID
            WHERE products.ProductName LIKE ?
            OR categories.CategoryName LIKE ?";

    $statement = $connection->prepare($sql);
    $statement->bind_param("ss", $searchValue, $searchValue);
    $statement->execute();

    $result = $statement->get_result();

} else {

    // Display all products
    $sql = "SELECT products.*, categories.CategoryName
            FROM products
            LEFT JOIN categories
            ON products.CategoryID = categories.CategoryID";

    $result = $connection->query($sql);
}

// Get categories for the add product form
$categories = $connection->query("SELECT * FROM categories");

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

    <!-- Display messages -->
    <?php if ($message != "") { ?>
        <p><?php echo $message; ?></p>
    <?php } ?>

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

    <hr>

    <!-- Only show Add Product to admins -->
    <?php if ($isAdmin) { ?>

        <h2>Add Product</h2>

        <!-- Add product form -->
        <form method="POST" action="inventory.php">

            <input
                type="text"
                name="productName"
                placeholder="Product Name"
                required
            >

            <input
                type="text"
                name="description"
                placeholder="Description"
                required
            >

            <input
                type="number"
                name="price"
                step="0.01"
                placeholder="Price"
                required
            >

            <select name="categoryID" required>

                <option value="">Select Category</option>

                <?php while ($category = $categories->fetch_assoc()) { ?>

                    <option value="<?php echo $category["CategoryID"]; ?>">
                        <?php echo htmlspecialchars($category["CategoryName"]); ?>
                    </option>

                <?php } ?>

            </select>

            <input
                type="text"
                name="condition"
                placeholder="Condition"
                required
            >

            <button type="submit" name="add_product">
                Add Product
            </button>

        </form>

        <hr>

    <?php } ?>

    <!-- Display products -->
    <h2>Products</h2>

    <?php if ($result->num_rows > 0) { ?>

        <?php while ($product = $result->fetch_assoc()) { ?>

            <div class="product">

                <!-- Product information -->
                <h2>
                    <?php echo htmlspecialchars($product["ProductName"]); ?>
                </h2>

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

                <!-- Only show Edit and Delete to admins -->
                <?php if ($isAdmin) { ?>

                    <h3>Edit Product</h3>

                    <!-- Edit product form -->
                    <form method="POST" action="inventory.php">

                        <input
                            type="hidden"
                            name="productID"
                            value="<?php echo $product["ProductID"]; ?>"
                        >

                        <input
                            type="text"
                            name="productName"
                            value="<?php echo htmlspecialchars($product["ProductName"]); ?>"
                            required
                        >

                        <input
                            type="text"
                            name="description"
                            value="<?php echo htmlspecialchars($product["Description"]); ?>"
                            required
                        >

                        <input
                            type="number"
                            name="price"
                            step="0.01"
                            value="<?php echo $product["Price"]; ?>"
                            required
                        >

                        <input
                            type="text"
                            name="condition"
                            value="<?php echo htmlspecialchars($product["ProductCondition"]); ?>"
                            required
                        >

                        <button type="submit" name="edit_product">
                            Edit Product
                        </button>

                    </form>

                    <!-- Delete product form -->
                    <form method="POST" action="inventory.php">

                        <input
                            type="hidden"
                            name="productID"
                            value="<?php echo $product["ProductID"]; ?>"
                        >

                        <button type="submit" name="delete_product">
                            Delete Product
                        </button>

                    </form>

                <?php } ?>

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
