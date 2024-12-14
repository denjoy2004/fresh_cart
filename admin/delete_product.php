<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Adjust the path as necessary

// Check if the product ID is set in the POST request
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Query to update the product's status instead of deleting it
    $update_status_query = "UPDATE product_table SET status = 'removed' WHERE product_id = ?";
    $stmt = $conn->prepare($update_status_query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to manage products page or show a success message
    header("Location: manage_products.php");
    exit();
}

$conn->close();
?>
