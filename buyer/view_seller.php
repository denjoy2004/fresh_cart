<?php
// Include the database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Adjust the path as necessary

// Initialize seller ID
$seller_id = $_POST['seller_id'] ?? ''; // Use POST method to get seller ID

// Check if seller ID is provided
if ($seller_id) {
    // Query to fetch seller details
    $seller_query = "
        SELECT seller_name 
        FROM seller_table 
        WHERE seller_username = ?";
    
    $stmt = $conn->prepare($seller_query);
    $stmt->bind_param('s', $seller_id);
    $stmt->execute();
    $seller_result = $stmt->get_result();

    // Check if seller exists
    if ($seller_result->num_rows > 0) {
        $seller = $seller_result->fetch_assoc();
        $seller_name = $seller['seller_name'];
    } else {
        // Seller not found
        $seller_name = "Unknown Seller"; // Default value if not found
    }
    
    $stmt->close();

    // Query to fetch products from seller
    $product_query = "
        SELECT p.product_id, p.product_name, p.price, p.stock_quantity, p.image_path 
        FROM product_table p 
        WHERE p.seller_id = ?";
    
    $stmt = $conn->prepare($product_query);
    $stmt->bind_param('s', $seller_id);
    $stmt->execute();
    $product_result = $stmt->get_result();
} else {
    // Handle case where seller ID is not provided
    $seller_name = "Unknown Seller"; // Default value if not provided
    $product_result = []; // Empty result for products
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products from Seller - Fresh Cart</title>
    <link rel="stylesheet" href="../css/view_seller.css"> 
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <a href="index.html">
                    <img src="../images/logo-no-background.png" width="200px" height="auto" alt="Fresh Cart Logo">
                </a>
            </div>
            <div class="menu">
                <nav>
                    <ul>
                        <li><a href="buyer_home.php">Home</a></li>
                        <li><a href="my_orders.php">My Orders</a></li>
                        <li><a href="account_settings.php">Account Settings</a></li>
                        <li><a href="browse_products.php"><i class="fa fa-shopping-cart" style="font-size:36px"></i></a></li>
                    </ul>
                </nav>
            </div>
            <button class="logout-btn">Logout</button>
        </header>

        <main class="main">
            <h1>Products from Seller: <?php echo htmlspecialchars($seller_name); ?></h1>
            <div class="products-grid">
                <?php if ($product_result->num_rows > 0): ?>
                    <?php while ($product = $product_result->fetch_assoc()): ?>
                        <div class="product-card">
                            <img src="../uploads/<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
                            <p class="price">&#8377;<?php echo htmlspecialchars($product['price']); ?></p>
                            <p class="stock">
                                <?php if ($product['stock_quantity'] > 0): ?>
                                    <span style="color: green;">In Stock</span>
                                <?php else: ?>
                                    <span style="color: red;">Out of Stock</span>
                                <?php endif; ?>
                            </p>
                            <div class="button-group">
                                <form action="product_detail.php" method="POST" class="view-details-form">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                                    <button type="submit" class="view-details-btn">View Details</button>
                                </form>
                                <button class="add-to-cart-btn">Add to Cart</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="product-card">
                        <h2>No Products Available</h2>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Fresh Cart. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
