<?php
// Start the session
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

// Include database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Adjust the path as necessary

// Check if the buyer_username is set in the GET request
if (isset($_POST['buyer_username'])) {
    $buyer_username = $_POST['buyer_username'];

    // Fetch buyer details from the database
    $query = "SELECT * FROM buyer_table WHERE buyer_username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $buyer_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $buyer = $result->fetch_assoc();
    } else {
        echo "Buyer not found.";
        exit;
    }

    // Fetch order details for the buyer
    $order_query = "SELECT o.order_id, o.total_amount, o.order_status, o.ordered_at 
                    FROM order_table AS o 
                    WHERE o.buyer_id = ?";
    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param("s", $buyer_username);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
} else {
    echo "No buyer specified.";
    exit;
}

// Do not close the connection yet, as we need it for fetching product details later
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Buyer - <?php echo htmlspecialchars($buyer['buyer_username']); ?></title>
    <link rel="stylesheet" href="../css/my_orders.css"> <!-- Link to my_orders.css -->
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
                        <li><a href="manage_buyers.php">Manage Buyers</a></li>
                    </ul>
                </nav>
            </div>
            <button class="logout-btn">Logout</button>
        </header>

        <main class="main">
            <h1>Buyer Details</h1>
            <div class="buyer-details">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($buyer['buyer_username']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($buyer['buyer_name']); ?></p>
                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($buyer['buyer_mbno']); ?></p>
                <p><strong>Address:</strong> 
                    <?php 
                        echo htmlspecialchars($buyer['buyer_house_name']) . ", " .
                             htmlspecialchars($buyer['buyer_area']) . ", " .
                             htmlspecialchars($buyer['buyer_city']) . ", " .
                             htmlspecialchars($buyer['buyer_state']) . " - " .
                             htmlspecialchars($buyer['buyer_pincode']);
                    ?>
                </p>
            </div>

            <h2>Order Details</h2>
            <?php if ($order_result->num_rows > 0): ?>
                <div class="orders-container">
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Order Status</th>
                                <th>Date of Order</th>
                                <th>Total Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order_row = $order_result->fetch_assoc()): ?>
                                <tr>
                                <td><?php echo htmlspecialchars($order_row['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order_row['order_status']); ?></td>
                                <td><?php echo htmlspecialchars(date('d-m-Y H:i:s', strtotime($order_row['ordered_at']))); ?></td>
                                <td>&#8377; <?php echo htmlspecialchars(number_format($order_row['total_amount'], 2)); ?></td>
                                <td>
                                    <button onclick="toggleDetails(<?php echo $order_row['order_id']; ?>)">View Details</button>                                    
                                </td>
                                </tr>

                                <!-- Order Details Row (Hidden by default) -->
                                <tr id="details-<?php echo $order_row['order_id']; ?>" style="display: none;">
                                    <td colspan="5">
                                        <div class="product-table-container">
                                            <table class="product-table">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Product Name</th>
                                                        <th>Description</th>
                                                        <th>Seller</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $order_id = $order_row['order_id'];
                                                    $product_sql = "SELECT oi.quantity, oi.price, p.product_name, p.description, s.seller_name, p.min_quantity, p.image_path
                                                                    FROM order_items_table AS oi 
                                                                    JOIN product_table AS p ON oi.product_id = p.product_id 
                                                                    JOIN seller_table AS s ON p.seller_id = s.seller_username
                                                                    WHERE oi.order_id = ?";
                                                    $product_stmt = $conn->prepare($product_sql);
                                                    $product_stmt->bind_param("i", $order_id);
                                                    $product_stmt->execute();
                                                    $product_result = $product_stmt->get_result();

                                                    while ($product_row = $product_result->fetch_assoc()) :
                                                    ?>
                                                        <tr>
                                                            <td><img src="../uploads/<?php echo htmlspecialchars($product_row['image_path']); ?>" style="width: 120px; height: 100px;"></td>
                                                            <td><?php echo htmlspecialchars($product_row['product_name']); ?></td>
                                                            <td><?php echo htmlspecialchars($product_row['description']); ?></td>
                                                            <td><?php echo htmlspecialchars($product_row['seller_name']); ?></td>
                                                            <td><?php echo htmlspecialchars($product_row['min_quantity']); ?> * <?php echo htmlspecialchars($product_row['quantity']); ?></td>
                                                            <td>₹<?php echo htmlspecialchars(number_format($product_row['price'], 2)); ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No orders found for this buyer.</p>
            <?php endif; ?>
        </main>
    </div>

    <script>
        let currentlyVisibleOrderId = null;

        function toggleDetails(orderId) {
            if (currentlyVisibleOrderId && currentlyVisibleOrderId !== orderId) {
                document.getElementById('details-' + currentlyVisibleOrderId).style.display = 'none';
            }

            const detailsRow = document.getElementById('details-' + orderId);
            const isCurrentlyVisible = detailsRow.style.display === 'table-row';

            detailsRow.style.display = isCurrentlyVisible ? 'none' : 'table-row';

            currentlyVisibleOrderId = isCurrentlyVisible ? null : orderId;
        }
    </script>
</body>
</html>

<?php
// Close the connection after all queries are done
$conn->close();