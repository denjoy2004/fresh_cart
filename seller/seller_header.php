<?php
// Start the session only if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the seller is logged in
if (!isset($_SESSION['seller_username'])) {
    // Redirect to the login page if not logged in
    header("Location: seller_login.php");
    exit();
}

// Get the seller's name from the session
$seller_name = $_SESSION['seller_name'];
?>
<!-- Font Awesome Kit for Icons -->
<script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
<link rel="stylesheet" href="../css/header.css">

<header>
    <div class="logo">
        <a href="seller_home.php">
            <img src="../images/logo-no-background.png" width="200px" alt="Fresh Cart Logo">
        </a>
    </div>
    <div class="menu">
        <nav>
            <ul>
                <li><a href="seller_home.php">Home</a></li>
                <li><a href="seller_products.php">My Products</a></li>
                <li><a href="add_product.php">Add New Product</a></li>
                <li><a href="update_seller_account.php">Update Account</a></li>
                <li><a href="sales_report.php">Sales Report</a></li>
            </ul>
        </nav>
    </div>
        
    <div class="welcome-message">
        <img src="../images/user_icon.png" width="20px" height="20px">
        <span><?php echo htmlspecialchars($seller_name); ?></span>
        <a href="seller_logout.php"><button class="logout-btn">Logout</button></a>
    </div>
</header>