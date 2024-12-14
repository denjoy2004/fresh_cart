<?php
session_start();

include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Adjust the path as necessary

// Check if the buyer is logged in
if (!isset($_SESSION['buyer_username'])) {
    header("Location: buyer_login.php");
    exit();
}

// Check if product ID and quantity are provided
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $buyer_username = $_SESSION['buyer_username'];

    // Assuming a cart table or session-based cart logic, add product to cart
    // Example: Insert into the cart or update if product already exists

    $cart_query = "
        INSERT INTO cart_table (buyer_id, product_id, quantity) 
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + ?";
    
    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param('siii', $buyer_username, $product_id, $quantity, $quantity);
    $stmt->execute();
    $stmt->close();

    // Redirect to product_details.php using POST method
    echo "
    <form id='redirectForm' action='product_detail.php' method='POST'>
        <input type='hidden' name='product_id' value='" . htmlspecialchars($product_id) . "'>
        <input type='hidden' name='redirect' value='true'>
    </form>
    <script>
        document.getElementById('redirectForm').submit();
    </script>";
}

$conn->close();
?>
