<?php
// Start session and check if buyer is logged in
session_start();
if (!isset($_SESSION['buyer_id'])) {
    header("Location: buyer_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard - Fresh Cart</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">    
        <header>
            <div class="logo">
                <a href="index.html"><img src="/fresh_cart/images/logo-no-background.png" width="200px" height="auto"></a>
            </div>
            <nav>
                <a href="buyer_profile.php">Profile</a> | 
                <a href="buyer_cart.php">Cart</a> | 
                <a href="buyer_orders.php">Orders</a> | 
                <a href="buyer_logout.php">Logout</a>
            </nav>
        </header>
        <h2>Welcome, <?php echo $_SESSION['buyer_name']; ?>!</h2>
        <h3>Available Products</h3>
        <!-- Product list logic -->
        <div class="products">
            <!-- Sample product display (this would be dynamically generated from the database) -->
            <div class="product">
                <img src="/fresh_cart/images/product1.jpg" alt="Product 1">
                <h4>Product 1</h4>
                <p>$10.00</p>
                <button>Add to Cart</button>
            </div>
            <!-- Repeat for more products -->
        </div>
    </div>
    <footer>&copy; 2024 Fresh Cart. All rights reserved.</footer>
</body>
</html>
