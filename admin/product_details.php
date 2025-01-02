<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Adjust the path as necessary

// Initialize product ID
$product_id = $_POST['product_id'] ?? ''; // Use POST method to get product ID

// Check if product ID is provided
if ($product_id) {
    // Query to fetch product details by ID, including seller information
    $product_query = "
        SELECT p.product_id, p.product_name, p.price, p.stock_quantity, p.min_quantity, p.image_path, s.seller_name, s.seller_username AS seller_id, p.description
        FROM product_table p
        JOIN seller_table s ON p.seller_id = s.seller_username
        WHERE p.product_id = ? AND p.status = 'active'";
    
    $stmt = $conn->prepare($product_query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();
    
    // Check if product exists
    if ($product_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();
    } else {
        // Product not found
        $product = null;
    }
    $stmt->close();
} else {
    // Handle case where product ID is not provided
    $product = null;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Admin View</title>
    <link rel="stylesheet" href="../css/product_detail.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <a href="admin_home.php">
                    <img src="../images/logo-no-background.png" width="200px" height="auto" alt="Fresh Cart Logo">
                </a>
            </div>
            <div class="menu">
                <nav>
                    <ul>
                        <li><a href="admin_home.php">Dashboard</a></li>
                        <li><a href="manage_products.php">Manage Products</a></li>
                        <li><a href="manage_sellers.php">Manage Sellers</a></li>
                    </ul>
                </nav>
            </div>
            <button class="logout-btn">Logout</button>
        </header>

        <main class="main">
            <?php if ($product): ?>
                <div class="product-detail">
                    <img src="../uploads/<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                    <div class="product-info">
                        <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                        <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="min_quantity">Minimum Quantity: <?php echo htmlspecialchars($product['min_quantity']); ?></p>
                        <p class="price">&#8377;<?php echo htmlspecialchars($product['price']); ?></p>
                        <p class="seller">Seller: <?php echo htmlspecialchars($product['seller_name']); ?></p>
                        <p class="stock">
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <span style="color: green;">In Stock</span>
                            <?php else: ?>
                                <span style="color: red;">Out of Stock</span>
                            <?php endif; ?>
                        </p>

                        <form action="delete_product.php" method="POST" class="remove-product-form">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                            <button type="submit" class="remove-product-btn">Remove Product</button>
                        </form>


                        <form action="view_seller_products.php" method="POST" class="view-seller-form">
                            <input type="hidden" name="seller_username" value="<?php echo htmlspecialchars($product['seller_id']); ?>">
                            <button type="submit" class="view-seller-btn">View More Products from This Seller</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="product-detail">
                    <h1>Product Not Found</h1>
                    <p>Sorry, the product you are looking for does not exist.</p>
                </div>
            <?php endif; ?>
            <?php include '../footer.php'; ?>
        </main>
    </div>
</body>
</html>
