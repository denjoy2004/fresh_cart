<?php
    session_start();

    // Check if the seller is logged in
    if (!isset($_SESSION['seller_username'])) {
        header("Location: seller_login.php");
        exit();
    }
    
    // Include the database connection
    include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php';
    
    // Retrieve the seller username
    $seller_username = $_SESSION['seller_username'];
    
    // Check if the form is submitted for editing a product
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
        // Get the product details from the form
        $productId = $_POST['product_id'];
        $productName = isset($_POST['product_name']) ? trim($_POST['product_name']) : null;
        $productPrice = isset($_POST['product_price']) ? trim($_POST['product_price']) : null;
        $productStock = isset($_POST['product_stock']) ? trim($_POST['product_stock']) : null;
        $productDescription = isset($_POST['product_description']) ? trim($_POST['product_description']) : null;

        // Check if all required fields are filled
        if (empty($productName) || empty($productPrice) || empty($productStock) || empty($productDescription)) {
            echo "Error: All fields are required.";
            exit();
        }
        
        // Check if a new product image is uploaded
        $productImage = null;
        if (!empty($_FILES['product_image']['name'])) {
            $productImage = $_FILES['product_image']['name'];
            $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/Fresh_Cart/uploads/";
            $targetFile = $targetDir . basename($productImage);
    
            // Move the uploaded file to the target directory
            if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFile)) {
                echo "Error uploading image.";
                exit();
            }
        }
    
        // Prepare the SQL query for updating the product
        if (!is_null($productImage)) {
            // Update the product with image
            $updateQuery = "UPDATE product_table SET product_name=?, price=?, stock_quantity=?, description=?, image_path=? WHERE product_id=?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param('sdissi', $productName, $productPrice, $productStock, $productDescription, $productImage, $productId);
        } else {
            // Update the product without image
            $updateQuery = "UPDATE product_table SET product_name=?, price=?, stock_quantity=?, description=? WHERE product_id=?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param('sdssi', $productName, $productPrice, $productStock, $productDescription, $productId);
        }
    
        // Execute the query and check if the update is successful
        if ($stmt->execute()) {
            // Redirect to the seller products page
            header("Location: seller_products.php");
            exit();
        } else {
            echo "Error updating product: " . $stmt->error;
        }
    }
    
    // Get the product ID from the POST request and fetch product details
    $productId = $_POST['product_id'];
    $productQuery = "SELECT * FROM product_table WHERE product_id = ?";
    $stmt = $conn->prepare($productQuery);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $productResult = $stmt->get_result();
    $product = $productResult->fetch_assoc();
    $stmt->close();
    
    // Close the database connection
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Fresh Cart</title>
    <link rel="stylesheet" href="/fresh_cart/css/edit_product.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="/fresh_cart/images/logo-no-background.png" alt="Fresh Cart Logo">
        </div>
        <div class="form-container">
            <h2>Edit Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <!-- Hidden field for product ID -->
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">

                <!-- Product Name Field -->
                <label for="product_name">Product Name</label>
                <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>

                <!-- Product Price Field -->
                <label for="product_price">Product Price ($)</label>
                <input type="number" step="0.01" id="product_price" name="product_price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

                <!-- Stock Quantity Field -->
                <label for="product_stock">Stock Quantity</label>
                <input type="number" id="product_stock" name="product_stock" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>

                <!-- Product Description Field -->
                <label for="product_description">Product Description</label>
                <textarea id="product_description" name="product_description" required><?php echo htmlspecialchars($product['description']); ?></textarea>

                <!-- Product Image Upload Field -->
                <label for="product_image">Product Image (Leave blank to keep existing)</label>
                <input type="file" id="product_image" name="product_image" accept="image/*">

                <!-- Submit and Back Buttons -->
                <button type="submit" name="edit_product">Update Product</button>
                <a href="seller_products.php"><button type="button" class="back-button">Back to Products</button></a>
            </form>
        </div>
    </div>
</body>
</html>
