<?php
// Include the database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Adjust the path as necessary

// Initialize search keyword and sorting option
$search_keyword = '';
$sort_option = 'default';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search'])) {
        $search_keyword = $_POST['search'];
    }

    if (isset($_POST['sort'])) {
        $sort_option = $_POST['sort'];
    }
}

// Base query to get all products from all sellers
$product_query = "
    SELECT p.product_id, p.product_name, p.price, p.stock_quantity, p.image_path, s.seller_name
    FROM product_table p
    JOIN seller_table s ON p.seller_id = s.seller_username
    WHERE p.product_name LIKE '%$search_keyword%' OR s.seller_name LIKE '%$search_keyword%'";

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
        <!-- Header Section -->
        <header>
            <div class="logo">
                <a href="index.html">
                    <img src="../images/logo-no-background.png" width="200px" height="auto" alt="Fresh Cart Logo">
                </a>
            </div>
            <form name="search-bar" action="products_list.php" method="POST" class="search-form">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
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
        
        <!-- Main Content Section -->
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
            </div>
            <section class="product-list">
                <div class="product-grid">
                    <?php if ($product_result->num_rows > 0): ?>
                        <?php while ($row = $product_result->fetch_assoc()): ?>
                            <div class="product-card">
                                <img src="../uploads/<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                                <p>Price: &#8377;<?php echo htmlspecialchars($row['price']); ?></p>
                                <p>Stock: <?php echo htmlspecialchars($row['stock_quantity']); ?></p>
                                <p>Seller: <?php echo htmlspecialchars($row['seller_name']); ?></p>
                                <form action="product_detail.php" method="POST" class="view-details-form">
                                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                    <button type="submit" class="btn">View Details</button>
                                </form>
                                <div class="button-group">
                                    <button class="add-to-cart-btn">Add to Cart <i class="fa fa-shopping-cart"></i></button>
                                    <button class="buy-btn">Buy <i class="fa fa-credit-card"></i></button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No products found.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
        
        <footer>
            <p>&copy; 2024 Fresh Cart. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
