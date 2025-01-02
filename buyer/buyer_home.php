<?php
session_start();

// Check if the seller is logged in
if (!isset($_SESSION['buyer_username'])) {
    header("Location: buyer_login.php");
    exit();
}

include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; 

// Handle Add to Cart functionality
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Initialize cart if not already set
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if product is already in the cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity; 
    } else {
        $_SESSION['cart'][$product_id] = $quantity; 
    }

    // Redirect to the same page to avoid resubmission
    header("Location: buyer_home.php"); 
    exit();
}

// Query to get the most sold products
$most_sold_query = "
    SELECT p.product_id, p.product_name, p.price, p.stock_quantity, p.description, p.image_path,
           COUNT(oi.order_item_id) AS order_count
    FROM product_table p
    LEFT JOIN order_items_table oi ON p.product_id = oi.product_id
    WHERE p.status = 'active'
    GROUP BY p.product_id
    ORDER BY order_count DESC
    LIMIT 5;";

$most_sold_result = $conn->query($most_sold_query);
$promotions = ["20% off on all fruits!", "Buy 2 get 1 free on vegetables!"]; // Example promotions
$testimonials = ["Great service!", "Loved the fresh produce!"]; // Example testimonials

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Home - Fresh Cart</title>
    <link rel="stylesheet" href="../css/buyer_home.css"> <!-- Make sure this CSS file exists -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="container">

        <?php include 'buyer_header.php'; ?>

        <main>
            <!-- Most Sold Products Section -->
            <section class="most-sold">
                <h2>Most Sold Products</h2>
                <div class="product-grid">
                    <?php if ($most_sold_result->num_rows > 0): ?>
                        <?php while ($row = $most_sold_result->fetch_assoc()): ?>
                            <div class="product-card">
                                <img src="../uploads/<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                                <p>Price: &#8377;<?php echo htmlspecialchars($row['price']); ?></p>
                                <p><?php echo htmlspecialchars($row['description']); ?></p>
                                <p class="stock">
                                    <?php if ($row['stock_quantity'] > 0): ?>
                                        <span style="color: green;">In Stock</span>
                                        <div class="button-group">
                                            <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                                <input type="number" name="quantity" min="1" value="1" required class="quantity-input">
                                                <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart <i class="fa fa-shopping-cart"></i></button>
                                            </form>
                                            <!-- View More Details button -->
                                            <form action="product_detail.php" method="POST" class="view-details-form">
                                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                                <button type="submit" class="view-details-btn">View Details</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: red;">Out of Stock</span>
                                        <div class="button-group">
                                            <form action="product_detail.php" method="POST" class="view-details-form">
                                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                                <button type="submit" class="view-details-btn">View Details</button>
                                            </form>
                                            <form class="add-to-cart-form">
                                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                                <input type="number" name="quantity" min="1" value="1" required class="quantity-input" readonly>
                                                <button type="button" class="add-to-cart-btn" onclick="alert('Product is out of stock and cannot be added to cart')">Add to Cart <i class="fa fa-shopping-cart"></i></button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No sold products found.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Categories Section -->
            <section class="categories">
                <h2>Categories</h2>
                <div class="image-grid">
                    <div class="image-item">
                        <a href="category.php?slug=fruits">
                            <img src="../images/category1.jpg" alt="Fruits">
                        </a>
                    </div>
                    <div class="image-item">
                        <a href="category.php?slug=vegetables">
                            <img src="../images/category2.jpg" alt="Vegetables">
                        </a>
                    </div>
                    <div class="image-item">
                        <a href="category.php?slug=dairy">
                            <img src="../images/category3.jpg" alt="Dairy">
                        </a>
                    </div>
                    <div class="image-item">
                        <a href="category.php?slug=meat">
                            <img src="../images/category4.jpg" alt="Meat">
                        </a>
                    </div>
                    <div class="image-item">
                        <a href="category.php?slug=beverages">
                            <img src="../images/category5.jpg" alt="Beverages">
                        </a>
                    </div>
                </div>
            </section>
                    <?php include '../footer.php'; ?>
        </main>
    </div>
</body>
</html>
