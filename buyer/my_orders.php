<?php
session_start();
if (!isset($_SESSION['buyer_username'])) {
    header("Location: buyer_login.php");
    exit();
}

// Include database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php';

$buyer_id = $_SESSION['buyer_username'];

// Fetch orders for the buyer
$order_sql = "SELECT o.order_id, o.total_amount, o.order_status, o.ordered_at 
              FROM order_table AS o 
              WHERE o.buyer_id = ? 
              ORDER BY o.ordered_at DESC";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("s", $buyer_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="../css/my_orders.css">
</head>

<body>
    <div class="container">
        
    <?php include 'buyer_header.php'; ?>

        <div class="orders-container">
            <h1>My Orders</h1>
            <?php if ($order_result->num_rows > 0) : ?>
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
                        <?php while ($order_row = $order_result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order_row['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order_row['order_status']); ?></td>
                                <td><?php echo htmlspecialchars(date('d-m-Y H:i:s', strtotime($order_row['ordered_at']))); ?></td>
                                <td>&#8377; <?php echo htmlspecialchars(number_format($order_row['total_amount'], 2)); ?></td>
                                <td>
                                    <!-- View Details Button -->
                                    <button onclick="toggleDetails(<?php echo $order_row['order_id']; ?>)">View Details</button>
                                    <!-- Cancel Order Button if order is pending -->
                                    <?php if ($order_row['order_status'] == 'pending') : ?>
                                        <form action="cancel_order.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order_row['order_id']; ?>">
                                            <button type="submit">Cancel Order</button>
                                        </form>
                                    <?php endif; ?>
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
            <?php else : ?>
                <p>No orders found.</p>
            <?php endif; ?>
        </div>

        <?php include '../footer.php'; ?>
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
