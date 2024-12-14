<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; 
$search_keyword = '';
$sort_option = isset($_POST['sort']) ? $_POST['sort'] : 'default';

// Sanitize and process search input

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
        header("Location: manage_prodcuts.php");
        exit();
    }
}
// Prepare the base query
$product_query = "
    SELECT p.product_id, p.product_name, p.price, p.stock_quantity, p.description, p.min_quantity, p.image_path, s.seller_name
    FROM product_table p
    JOIN seller_table s ON p.seller_id = s.seller_username
    WHERE (p.product_name LIKE ? OR s.seller_name LIKE ?)
    AND p.status = 'active'
";

// Apply sorting
if ($sort_option === 'price-low-to-high') {
    $product_query .= " ORDER BY CAST(p.price AS DECIMAL(10, 2)) ASC"; // Sort by price low to high
} elseif ($sort_option === 'price-high-to-low') {
    $product_query .= " ORDER BY CAST(p.price AS DECIMAL(10, 2)) DESC"; // Sort by price high to low
}

// Prepare statement
$stmt = $conn->prepare($product_query);
$search_param = "%$search_keyword%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();

// Get results
$product_result = $stmt->get_result();
$stmt->close();
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
        <header>
            <div class="logo">
                <a href="admin_home.php">
                    <img src="../images/logo-no-background.png" width="200px" height="auto" alt="Fresh Cart Logo">
                </a>
            </div>
            <div class="menu">
                <nav>
                <ul>
                        <li><a href="manage_products.php">Manage Products</a></li>
                        <li><a href="manage_buyers.php">Manage Buyers</a></li>
                        <li><a href="manage_sellers.php">Manage Sellers</a></li>
                        <li><a href="manage_orders.php">Manage Orders</a></li>
                        <li><a href="reports.php">Reports</a></li>
                    </ul>
                </nav>
            </div>
            <a href="admin_logout.php"><button class="logout-btn">Logout</button></a>
        </header>

        <main>
            <div class="sort-container">
                
                <label for="sort-options">Sort by:</label>
                <form action="manage_products.php" method="POST">
                    <select id="sort-options" name="sort" onchange="this.form.submit()">
                        <option value="default" <?php echo $sort_option === 'default' ? 'selected' : ''; ?>>All Products</option>
                        <option value="price-low-to-high" <?php echo $sort_option === 'price-low-to-high' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price-high-to-low" <?php echo $sort_option === 'price-high-to-low' ? 'selected' : ''; ?>>Price: High to Low</option>
                    </select>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_keyword); ?>">
                </form>
                <form name="search-bar" action="manage_products.php" method="POST" class="search-form">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
            </div>

            <section class="product-list">
                <div class="product-grid">
                    <?php if ($product_result->num_rows > 0): ?>
                        <?php while ($row = $product_result->fetch_assoc()): ?>
                            <div class="product-card">
                                <img src="../uploads/<?php echo $row['image_path']; ?>" alt="<?php echo $row['product_name']; ?>">
                                <h3><?php echo $row['product_name']; ?></h3>
                                <p><?php echo htmlspecialchars($row['description']); ?></p>
                                <p>Minimum Quantity: <?php echo htmlspecialchars($row['min_quantity']); ?></p>
                                <p>Price: &#8377;<?php echo number_format($row['price'], 2); ?></p>
                                <p class="stock">
                                    <?php if ($row['stock_quantity'] > 0): ?>
                                        <span style="color: green;">In Stock</span>
                                        <div class="button-group">
                                            <form action="product_details.php" method="POST" class="view-details-form">
                                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                                <button type="submit" class="view-details-btn">View Details</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: red;">Out of Stock</span>
                                        <div c="button-group">
                                            <form action="product_details.php" method="POST" class="view-details-form">
                                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                                <button type="submit" class="view-details-btn">View Details</button>
                                            </form>  
                                        </div>
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No products found.</p>
                    <?php endif; ?>
                </div>
            </section>

            <?php include '../footer.php'; ?>

        </main>
    </div>
</body>
</html>
