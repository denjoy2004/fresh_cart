<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['seller_username'])) {
    header("Location: seller_login.php"); // Redirect to login page if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - Fresh Cart</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <a href="index.html"><img src="/fresh_cart/images/logo-no-background.png" width="200px" height="auto"></a>
            </div>
        </header>
        <div class="page1-img">
            <img src="/fresh_cart/images/seller_dashboard.jpg" width="100%" height="50%">
        </div>
        <div class="dashboard">
            <h2>Welcome, <?php echo $_SESSION['seller_name']; ?></h2>
            <p>Here you can manage your store, products, and view orders.</p>

            <ul>
                <li><a href="seller_profile.php">Profile</a></li>
                <li><a href="seller_products.php">Manage Products</a></li>
                <li><a href="seller_orders.php">View Orders</a></li>
                <li><a href="seller_sales.php">Sales Report</a></li>
                <li><a href="seller_logout.php">Logout</a></li>
            </ul>
        </div>
    </div>

    <section class="contact-info">
        <h2>Contact Information</h2>
        <address>
            Fresh Cart<br>
            Kochi<br>
            Kerala, 686582<br>
            Phone: <a href="tel:9539658310">+91 9539658310</a><br>
            Email: <a href="mailto:freshcart@gmail.com">freshcart@gmail.com</a>
        </address>
    </section>

    <footer>&copy; Copyright 2024 Fresh Cart. All rights reserved.</footer>
</body>
</html>
