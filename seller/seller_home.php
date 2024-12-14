<?php 
session_start();

// Check if the seller is logged in
if (!isset($_SESSION['seller_username'])) {
    header("Location: seller_login.php");
    exit();
}

// Include database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Ensure this path is correct

// Prepare SQL queries
$seller_username = $_SESSION['seller_username'];

// Query to get order details
$orderDetailsQuery = "
    SELECT oi.order_item_id, oi.order_id, oi.product_id, p.product_name, oi.quantity, oi.price, oi.order_status, b.buyer_name
    FROM order_items_table oi
    JOIN product_table p ON oi.product_id = p.product_id
    JOIN buyer_table b ON oi.buyer_id = b.buyer_username
    WHERE oi.seller_id = ? AND p.status = 'active'";
$orderDetailsStmt = $conn->prepare($orderDetailsQuery);
$orderDetailsStmt->bind_param('s', $seller_username);
$orderDetailsStmt->execute();
$orderDetailsResult = $orderDetailsStmt->get_result();

// Handle status update
// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_item_id']) && isset($_POST['order_status'])) {
    $orderItemId = $_POST['order_item_id'];
    $newStatus = $_POST['order_status'];

    // Update the status in the database
    $updateStatusQuery = "UPDATE order_items_table SET order_status = ? WHERE order_item_id = ? AND seller_id = ?";
    $stmt = $conn->prepare($updateStatusQuery);
    $stmt->bind_param('sis', $newStatus, $orderItemId, $seller_username);

    if ($stmt->execute()) {
        // Get the order ID from the updated order item
        $orderDetailsQuery = "SELECT order_id FROM order_items_table WHERE order_item_id = ?";
        $stmtOrder = $conn->prepare($orderDetailsQuery);
        $stmtOrder->bind_param('i', $orderItemId);
        $stmtOrder->execute();
        $orderResult = $stmtOrder->get_result();
        $orderRow = $orderResult->fetch_assoc();
        $orderId = $orderRow['order_id'];

        // Query to check the status of all items in the order
        $itemStatusQuery = "SELECT order_status FROM order_items_table WHERE order_id = ?";
        $stmtItems = $conn->prepare($itemStatusQuery);
        $stmtItems->bind_param('i', $orderId);
        $stmtItems->execute();
        $itemsResult = $stmtItems->get_result();

        $allDelivered = true;
        $anyPendingOrShipped = false;

        while ($item = $itemsResult->fetch_assoc()) {
            if ($item['order_status'] !== 'delivered') {
                $allDelivered = false;
            }
            if ($item['order_status'] === 'pending' || $item['order_status'] === 'shipped') {
                $anyPendingOrShipped = true;
            }
        }

        // Determine the overall order status
        if ($allDelivered) {
            $overallStatus = 'delivered';
        } elseif ($anyPendingOrShipped) {
            $overallStatus = 'In Progress';
        } else {
            $overallStatus = 'Completed'; // Optional status if needed
        }

        // Update the overall order status
        $updateOrderStatusQuery = "UPDATE order_table SET order_status = ? WHERE order_id = ?";
        $stmtUpdateOrder = $conn->prepare($updateOrderStatusQuery);
        $stmtUpdateOrder->bind_param('si', $overallStatus, $orderId);
        $stmtUpdateOrder->execute();

        echo "<script>alert('Order status updated successfully!');</script>";
        header("Refresh:0");
    } else {
        echo "<script>alert('Error updating order status.');</script>";
    }
    $stmt->close();
}


// Close the database connection
$conn->close();
?>

<!-- Your HTML content follows here -->

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
                    <li><a href="seller_products.php">Products</a></li>
                    <li><a href="add_product.php">Add Products</a></li>
                    <li><a href="sales_report.php">Sales Report</a></li>
                    <li><a href="update_seller_account.php">Update Profile</a></li>
                    </ul>
                </nav>
            </div>
            <button class="logout-btn" onclick="logout()">Logout</button>
        </header>

        <!-- Order Details Table Section -->
        <section class="orders">
            <h3>Order Details</h3>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order Item ID</th>
                        <th>Order ID</th>
                        <th>Buyer Name</th>
                        <th>Product Name</th>
                        <th>Product ID</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orderDetailsResult->num_rows > 0) : ?>
                        <?php while ($order = $orderDetailsResult->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $order['order_item_id']; ?></td>
                                <td><?php echo $order['order_id']; ?></td>
                                <td><?php echo $order['buyer_name']; ?></td>
                                <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                <td><?php echo $order['product_id']; ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td><?php echo '$' . number_format($order['price'], 2); ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="order_item_id" value="<?php echo $order['order_item_id']; ?>">
                                        <select name="order_status" class="dropdown">
                                            <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="shipped" <?php echo $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="delivered" <?php echo $order['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        </select>
                                </td>
                                <td>
                                        <button type="submit" class="save-btn">Save</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="9">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
        <?php include '../footer.php'; ?>
    </div>
</body>
</html>
