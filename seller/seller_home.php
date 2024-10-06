<?php
session_start();

// Check if the seller is logged in
if (!isset($_SESSION['seller_username'])) {
    header("Location: seller_home.php");
    exit();
}

// Include database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Ensure this path is correct

// Prepare SQL queries
$seller_username = $_SESSION['seller_username'];

// Query to get total products
$totalProductsQuery = "SELECT COUNT(*) AS total_products FROM product_table WHERE seller_id= '$seller_username'";
// Query to get total sales
$totalSalesQuery = "SELECT SUM(total_amount * quantity) AS total_sales FROM order_table WHERE seller_id = '$seller_username'";
// Query to get pending orders
$pendingOrdersQuery = "SELECT COUNT(*) AS pending_orders FROM order_table WHERE seller_id = '$seller_username' AND status = 'pending'";
// Query to get shipped orders
$shippedOrdersQuery = "SELECT COUNT(*) AS shipped_orders FROM order_table WHERE seller_id = '$seller_username' AND status = 'shipped'";

// Execute queries and fetch data
$totalProductsResult = $conn->query($totalProductsQuery);
$totalSalesResult = $conn->query($totalSalesQuery);
$pendingOrdersResult = $conn->query($pendingOrdersQuery);
$shippedOrdersResult = $conn->query($shippedOrdersQuery);

$totalProducts = $totalProductsResult->fetch_assoc()['total_products'];
$totalSales = $totalSalesResult->fetch_assoc()['total_sales'] ?? 0; // Default to 0 if null
$pendingOrders = $pendingOrdersResult->fetch_assoc()['pending_orders'];
$shippedOrders = $shippedOrdersResult->fetch_assoc()['shipped_orders'];

// Query to get order details
$orderDetailsQuery = "SELECT order_id, quantity, total_amount, status FROM order_table WHERE seller_id = '$seller_username'";
$orderDetailsResult = $conn->query($orderDetailsQuery);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['order_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['order_status'];
    
    // Update the status in the database
    $updateStatusQuery = "UPDATE order_table SET status = ? WHERE order_id = ? AND seller_id = ?";
    $stmt = $conn->prepare($updateStatusQuery);
    $stmt->bind_param('sis', $newStatus, $orderId, $seller_username);
    
    if ($stmt->execute()) {
        echo "<script>alert('Order status updated successfully!');</script>";
        // Refresh the page to show the updated status
        header("Refresh:0");
    } else {
        echo "<script>alert('Error updating order status.');</script>";
    }
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Cart - Seller Home</title>
    <link rel="stylesheet" href="/fresh_cart/css/seller_home.css">
    <script>
        function logout() {
            window.location.href = 'seller_logout.php';  // Redirects to the PHP file
        }
    </script>
</head>
<body>
    <div class="container">    

        <header>
            <div class="logo">
                <a href="index.html">
                    <img src="/fresh_cart/images/logo-no-background.png" width="200px" height="auto" alt="Fresh Cart Logo">
                </a>
            </div>
            <div class="menu">
                <nav>
                    <ul>
                        <li><a href="#dashboard">Dashboard</a></li>
                        <li><a href="seller_products.php">Your Products</a></li>
                        <li><a href="#">Sales Report</a></li>
                        <button class="logout-btn" onclick="logout()">Logout</button>
                    </ul>
                </nav>
            </div> 
        </header>

        <div class="welcome">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['seller_name']); ?>!</h2>
        </div>

        <section class="dashboard" id="dashboard">
            <h3>Your Dashboard</h3>
            <div class="dashboard-grid">
                <div class="dashboard-item">
                    <h4>Total Products</h4>
                    <p><?php echo $totalProducts; ?></p>
                </div>
                <div class="dashboard-item">
                    <h4>Total Sales</h4>
                    <p><?php echo '$' . number_format($totalSales, 2); ?></p>
                </div>
                <div class="dashboard-item">
                    <h4>Pending Orders</h4>
                    <p><?php echo $pendingOrders; ?></p>
                </div>
                <div class="dashboard-item">
                    <h4>Shipped Orders</h4>
                    <p><?php echo $shippedOrders; ?></p>
                </div>
            </div>
        </section>

        <!-- Order Details Table Section -->
        <section class="orders">
            <h3>Order Details</h3>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orderDetailsResult->num_rows > 0) : ?>
                        <?php while ($order = $orderDetailsResult->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td><?php echo '$' . number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <select name="order_status">
                                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        </select>
                                </td>
                                <td>
                                        <button type="submit">Save</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <div class="about" id="about">
            <h1>About Us</h1>
            <p>
                "Fresh Cart" is an online marketplace dedicated to facilitating the buying and selling of fresh fruits and vegetables. Our platform offers a seamless experience for both sellers and buyers, providing a convenient avenue to access high-quality produce. With a user-friendly interface, customers can browse through a diverse range of fruits and vegetables sourced directly from local farmers and trusted suppliers. From seasonal favorites to exotic varieties, Fresh Cart ensures freshness and quality with every purchase. Whether you're a farmer looking to sell your harvest or a consumer seeking the finest produce, Fresh Cart is your go-to destination for all things fresh and delicious.
            </p>
        </div>

        <section class="contact-info" id="contact">
            <h2>Contact Information</h2>
            <address>
                Fresh Cart<br>
                Kochi<br>
                Kerala, 686582<br>
                Phone: <a href="tel:+919539658310">+91 9539658310</a><br>
                Email: <a href="mailto:freshcart@gmail.com">freshcart@gmail.com</a>
            </address>
        </section>

        <footer>&copy; Copyright 2024 Fresh Cart. All rights reserved.</footer>
    </div>
</body>
</html>
