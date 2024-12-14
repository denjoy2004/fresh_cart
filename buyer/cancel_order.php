<?php
session_start();
if (!isset($_SESSION['buyer_username'])) {
    header("Location: buyer_login.php");
    exit();
}

include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    // Update the order status to 'cancelled'
    $cancel_sql = "UPDATE order_table SET order_status = 'cancelled' WHERE order_id = ? AND order_status != 'delivered'";
    $stmt = $conn->prepare($cancel_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Order cancelled successfully.";
    } else {
        $_SESSION['message'] = "Failed to cancel order or it cannot be cancelled.";
    }

    header("Location: my_orders.php"); // Redirect back to orders page
    exit();
}
?>
