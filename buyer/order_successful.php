<?php
session_start();
if (!isset($_SESSION['buyer_username'])) {
    header("Location: buyer_login.php");
    exit();
}

// Include database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php';

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$buyer_id = $_SESSION['buyer_username'];
$total_amount = $_POST['total_price'] ?? 0;
$payment_method = $_POST['payment_method'];

// Initialize message variable
$message = "";

// Fetch the cart items for the buyer
$cart_sql = "SELECT c.product_id, c.quantity, p.price, p.seller_id 
             FROM cart_table AS c 
             JOIN product_table AS p ON c.product_id = p.product_id 
             WHERE c.buyer_id = ?";
$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("s", $buyer_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

// Check if the cart is empty
if ($cart_result->num_rows === 0) {
    $message = "Your cart is empty. Please add items before placing an order.";
} else {
    // Insert into order_table
    $order_sql = "INSERT INTO order_table (buyer_id, total_amount, order_status, payment_method) VALUES (?, ?, 'pending', ?)";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("sds", $buyer_id, $total_amount, $payment_method);

    if (!$order_stmt->execute()) {
        $message = "Error placing order: " . $order_stmt->error;
    } else {
        // Retrieve the last inserted order ID
        $order_id = $conn->insert_id;

        // Insert each item into order_items_table
        while ($cart_row = $cart_result->fetch_assoc()) {
            $product_id = $cart_row['product_id'];
            $seller_id = $cart_row['seller_id'];
            $quantity = $cart_row['quantity'];
            $price = $quantity * $cart_row['price'];

            // Insert into order_items_table
            $order_item_sql = "INSERT INTO order_items_table (order_id, product_id, seller_id, buyer_id, quantity, price, order_status) 
                               VALUES (?, ?, ?, ?, ?, ?, 'pending')";
            $order_item_stmt = $conn->prepare($order_item_sql);
            $order_item_stmt->bind_param("iissid", $order_id, $product_id, $seller_id, $buyer_id, $quantity, $price);

            if (!$order_item_stmt->execute()) {
                $message .= "Error inserting order item: " . $order_item_stmt->error . "<br>";
            }
            
            $update_stock_sql = "UPDATE product_table SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
            $update_stock_stmt = $conn->prepare($update_stock_sql);
            $update_stock_stmt->bind_param("ii", $quantity, $product_id);
            $update_stock_stmt->execute();

        }

        // Remove all products from the cart
        $delete_cart_sql = "DELETE FROM cart_table WHERE buyer_id = ?";
        $delete_cart_stmt = $conn->prepare($delete_cart_sql);
        $delete_cart_stmt->bind_param("s", $buyer_id);

        if (!$delete_cart_stmt->execute()) {
            $message .= "Error removing items from cart: " . $delete_cart_stmt->error . "<br>";
        }

        // Set success message
        $message = "Order successfully placed! Your Order ID is: " . $order_id;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="../css/order_successful.css">
</head>
<body>
    <div class="container">
        
        <?php include 'buyer_header.php'; ?>

        <!-- Display the success message -->
        <div class="success-actions">
            <h1 class="message"><?php echo htmlspecialchars($message); ?></h1>
            <a href="my_orders.php" class="view-orders-btn">
                <span class="btn-icon">📦</span>
                View My Orders
            </a>
        </div>

        <?php include '../footer.php'; ?>
    </div>
</body>
</html>
