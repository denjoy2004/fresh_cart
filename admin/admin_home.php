<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_home.php");
    exit();
}

// Include database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Ensure this path is correct

// Query to get recent orders
$recent_orders_query = "
    SELECT o.order_id, o.ordered_at, b.buyer_username as buyer_name, o.total_amount, o.order_status
    FROM order_table o
    JOIN buyer_table b ON o.buyer_id = b.buyer_username
    ORDER BY o.ordered_at DESC
    LIMIT 5;";

$recent_orders_result = $conn->query($recent_orders_query);

// Query to get most sold products
$most_sold_query = "
    SELECT p.product_id, p.product_name, p.price, p.description, p.stock_quantity, p.image_path, SUM(oi.quantity) AS total_sold
    FROM product_table p
    JOIN order_items_table oi ON p.product_id = oi.product_id
    GROUP BY p.product_id
    ORDER BY total_sold DESC
    LIMIT 5;";

$most_sold_result = $conn->query($most_sold_query);

// Fetch total users, products, orders, and revenue
$total_buyers_query = "SELECT COUNT(*) AS total_buyers FROM buyer_table";
$total_buyers_result = $conn->query($total_buyers_query);
$total_buyers = $total_buyers_result->fetch_assoc()['total_buyers'];

$total_sellers_query = "SELECT COUNT(*) AS total_sellers FROM seller_table";
$total_sellers_result = $conn->query($total_sellers_query);
$total_sellers = $total_sellers_result->fetch_assoc()['total_sellers'];

$total_products_query = "SELECT COUNT(*) AS total_products FROM product_table";
$total_products_result = $conn->query($total_products_query);
$total_products = $total_products_result->fetch_assoc()['total_products'];

$total_orders_query = "SELECT COUNT(*) AS total_orders FROM order_table";
$total_orders_result = $conn->query($total_orders_query);
$total_orders = $total_orders_result->fetch_assoc()['total_orders'];

$total_revenue_query = "SELECT SUM(total_amount) AS total_revenue FROM order_table";
$total_revenue_result = $conn->query($total_revenue_query);
$total_revenue = $total_revenue_result->fetch_assoc()['total_revenue'];
// Close the connection after querying
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home - Fresh Cart</title>
    <link rel="stylesheet" href="../css/admin_home.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
<body>
    <div class="container">
        <?php include 'admin_header.php'; ?>
        <main>
            <!-- Dashboard Summary -->
            <section class="dashboard-summary">
                <h2>Dashboard Summary</h2>
                <div class="summary-grid">
                    <div class="summary-card">
                        <h3>Total Buyers</h3>
                        <p><?php echo $total_buyers; ?></p>
                    </div>
                    <div class="summary-card">
                        <h3>Total Sellers</h3>
                        <p><?php echo $total_sellers; ?></p>
                    </div>
                    <div class="summary-card">
                        <h3>Total Products</h3>
                        <p><?php echo $total_products; ?></p>
                    </div>
                    <div class="summary-card">
                        <h3>Total Orders</h3>
                        <p><?php echo $total_orders; ?></p>
                    </div>
                    <div class="summary-card">
                        <h3>Total Revenue</h3>
                        <p>₹<?php echo number_format($total_revenue); ?></p>
                    </div>
                </div>
            </section>

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
                                    <?php else: ?>
                                        <span style="color: red;">Out of Stock</span>
                                    <?php endif; ?>
                                </p>
                                <div class="button-group">
                                            <form action="product_details.php" method="POST" class="view-details-form">
                                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                                <button type="submit" class="view-details-btn">View Details</button>
                                            </form>
                                        </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No sold products found.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
        <?php require '../footer.php'; ?>    </div>
</body>
</html>
