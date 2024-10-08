<?php
session_start();

// Check if the seller is logged in
if (!isset($_SESSION['seller_username'])) {
    header("Location: seller_login.php");
    exit();
}

// Include database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Ensure this path is correct

$seller_username = $_SESSION['seller_username'];

// Handle adding a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];
    $productStock = $_POST['product_stock'];
    $productDescription = $_POST['product_description'];
    $productImage = $_FILES['product_image']['name'];
    $productImageTmp = $_FILES['product_image']['tmp_name'];
    $imageError = $_FILES['product_image']['error'];

    // Check for upload errors
    if ($imageError === UPLOAD_ERR_OK) {
        // Validate file type
        $imageFileType = strtolower(pathinfo($productImage, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowedTypes)) {
            echo "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        } else {
            // Corrected path with backslashes and directory separator
            $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/Fresh_Cart/uploads/"; // Absolute path
            $targetFile = $targetDir . basename($productImage);

            // Ensure the uploads directory exists
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);  // Create the directory if it doesn't exist
            }

            // Move uploaded image to target directory
            if (move_uploaded_file($productImageTmp, $targetFile)) {
                // Prepare the insert query
                $addProductQuery = "INSERT INTO product_table (product_name, seller_id, price, description, stock_quantity, image_path) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($addProductQuery);

                // Ensure the statement was prepared successfully
                if ($stmt) {
                    // Use appropriate types for the parameters: 'sdisss' for string, decimal, and integer
                    $stmt->bind_param('ssssis', $productName, $seller_username, $productPrice, $productDescription, $productStock, $productImage);

                    if ($stmt->execute()) {
                        // Redirect to avoid resubmission on refresh
                        header("Location: seller_products.php");
                        exit();
                    } else {
                        echo "Error: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    echo "Error preparing statement: " . $conn->error;
                }
            } else {
                echo "Error uploading image.";
            }
        }
    } else {
        echo "Error uploading file: " . $imageError;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Fresh Cart</title>
    <link rel="stylesheet" href="/fresh_cart/css/add_product.css"> <!-- Link to the CSS file -->
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="/fresh_cart/images/logo-no-background.png" alt="Fresh Cart Logo">
        </div>
        <div class="form-container">
            <h2>Add New Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="product_name">Product Name</label>
                <input type="text" id="product_name" name="product_name" required>

                <label for="product_price">Product Price (₹)</label>
                <input type="number" step="0.01" id="product_price" name="product_price" required>

                <label for="product_stock">Stock Quantity</label>
                <input type="number" id="product_stock" name="product_stock" required>

                <label for="product_description">Product Description</label>
                <textarea id="product_description" name="product_description" placeholder="Describe your product" required></textarea>

                <label for="product_image">Product Image</label>
                <input type="file" id="product_image" name="product_image" accept="image/*" required>

                <button type="submit" name="add_product">Add Product</button>
                <a href="seller_products.php"><button type="button" class="back-button">Back to Products</button></a>
            </form>
        </div>
    </div>
</body>
</html>
