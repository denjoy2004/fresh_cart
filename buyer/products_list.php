<?php
session_start();

// Check if the buyer is logged in
if (!isset($_SESSION['buyer_username'])) {
    header("Location: buyer_login.php");
    exit();
}

// Include the database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Adjust the path as necessary

// Initialize search keyword and sorting option
$search_keyword = '';
$sort_option = 'default';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search'])) {
        $search_keyword = $_POST['search'];
    }

    if (isset($_POST['sort'])) {
        $sort_option = $_POST['sort'];
    }

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
            $_SESSION['cart'][$product_id] += $quantity; // Increase quantity
        } else {
            $_SESSION['cart'][$product_id] = $quantity; // Add new product
        }

        // Redirect to the same page to avoid resubmission
        header("Location: products_list.php");
        exit();
    }
}

// Base query to get all products from all sellers
$product_query = "
    SELECT p.product_id, p.product_name, p.price, p.stock_quantity, p.description, p.min_quantity, p.image_path, s.seller_name
    FROM product_table p
    JOIN seller_table s ON p.seller_id = s.seller_username
    WHERE (p.product_name LIKE '%$search_keyword%' OR s.seller_name LIKE '%$search_keyword%') AND p.status = 'active'";

// Append sorting logic to the query
if ($sort_option === 'price-low-to-high') {
    $product_query .= " ORDER BY CAST(p.price AS DECIMAL(10, 2)) ASC"; // Sort by price low to high
} elseif ($sort_option === 'price-high-to-low') {
    $product_query .= " ORDER BY CAST(p.price AS DECIMAL(10, 2)) DESC"; // Sort by price high to low
}

$product_result = $conn->query($product_query);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product View - Fresh Cart</title>
    <link rel="stylesheet" href="../css/product_list.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="container">
        <?php include 'buyer_header.php'; ?>
        <main>
            <div class="sort-container">
                <label for="sort-options">Sort by:</label>
                <form action="products_list.php" method="POST">
                    <select id="sort-options" name="sort" onchange="this.form.submit()">
                        <option value="default" <?php echo $sort_option === 'default' ? 'selected' : ''; ?>>All Products</option>
                        <option value="price-low-to-high" <?php echo $sort_option === 'price-low-to-high' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price-high-to-low" <?php echo $sort_option === 'price-high-to-low' ? 'selected' : ''; ?>>Price: High to Low</option>
                    </select>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_keyword); ?>">
                </form>
                <form name="search-bar" action="products_list.php" method="POST" class ```php
                ="search-form">
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>

            <div class="product-list">
                <?php if ($product_result->num_rows > 0): ?>
                    <?php while ($product = $product_result->fetch_assoc()): ?>
                        <div class="product-item">
                            <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <p>Seller: <?php echo htmlspecialchars($product['seller_name']); ?></p>
                            <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
                            <p>Available Stock: <?php echo htmlspecialchars($product['stock_quantity']); ?></p>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                            <form action="products_list.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                                <input type="number" name="quantity" min="1" max="<?php echo htmlspecialchars($product['stock_quantity']); ?>" value="1">
                                <button type="submit" name="add_to_cart">Add to Cart</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No products found.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>