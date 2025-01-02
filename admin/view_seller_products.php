<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

// Include the database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Adjust the path as necessary

// Initialize seller ID
$seller_id = $_POST['seller_username'] ?? ''; // Use POST method to get seller ID

// Check if seller ID is provided
if ($seller_id) {
    // Query to fetch seller details
    $seller_query = "
        SELECT seller_name, seller_mbno, business_name, seller_area, seller_city, seller_state, seller_pincode
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
        $seller_mbno = $seller['seller_mbno'];
        $business_name = $seller['business_name'];
        $seller_area = $seller['seller_area'];
        $seller_city = $seller['seller_city'];
        $seller_state = $seller['seller_state'];
        $seller_pincode = $seller['seller_pincode'];
    } else {
        // Seller not found
        $seller_name = "Unknown Seller"; // Default value if not found
        $seller_mbno = $business_name = $seller_area = $seller_city = $seller_state = $seller_pincode = "Not Available"; // Default values
    }
    
    $stmt->close();

    // Query to fetch products from seller
    $product_query = "
        SELECT p.product_id, p.product_name, p.price, p.stock_quantity, p.image_path, p.description 
        FROM product_table p 
        WHERE p.seller_id = ? AND p.status = 'active'";
    
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
    <link rel="stylesheet" href="../css/view_seller.css"> <!-- Link to external CSS file -->
</head>
<body>
    <div class="container">
        <?php include 'C:\xampp\htdocs\Fresh_Cart\admin\admin_header.php'; ?>
        <main class="main">
            <h1>Seller: <?php echo htmlspecialchars($seller_name); ?></h1>
            
            <!-- Display Seller Information -->
            <div class="seller-info">
                <h2>Seller Details:</h2>
                <div class="contact-details">
                    <p><strong>Business Name:</strong> <span class="business-name"><?php echo htmlspecialchars($business_name); ?></span></p>
                    <p><strong>Contact Number:</strong> <span class="phone-number"><?php echo htmlspecialchars($seller_mbno); ?></span></p>
                    <p><strong>Email:</strong> <span class="email"><?php echo htmlspecialchars($seller_id); ?>@freshcart.com</span></p>
                    <p class="location"><strong>Location:</strong> <?php echo htmlspecialchars($seller_area); ?>, <?php echo htmlspecialchars($seller_city); ?>, <?php echo htmlspecialchars($seller_state); ?> - <?php echo htmlspecialchars($seller_pincode); ?></p>
                </div>
            </div>

            <!-- Display Seller Products -->
            <h2>Products Offered:</h2>
            <div class="products-grid">
                <?php if ($product_result->num_rows > 0): ?>
                    <?php while ($product = $product_result->fetch_assoc()): ?>
                        <div class="product-card">
                            <img src="../uploads/<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image">
                            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                            <p><strong>Price:</strong>  &#8377;<?php echo htmlspecialchars($product['price']); ?></p>
                            <p><strong>Stock Quantity:</strong> <?php echo htmlspecialchars($product['stock_quantity']); ?></p>
                            <div class="button-group">
                                <form method="POST" action="view_product.php">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                                    <button class="view-details-btn" type="submit">View Details</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No products available.</p>
                <?php endif; ?>
            </div>
        </main>
        <?php include '../footer.php'; ?>

    </div>
</body>
</html>
