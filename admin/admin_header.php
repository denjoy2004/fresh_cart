<?php
// Start the session only if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    // Redirect to the login page if not logged in
    header("Location: admin_login.php");
    exit();
}

// Get the admin's name from the session
$admin_name = $_SESSION['admin_name'];
?>
<link rel="stylesheet" href="../css/header.css">

<header>
    <div class="logo">
        <a href="admin_home.php">
            <img src="../images/logo-no-background.png" width="200px" height="auto" alt="Fresh Cart Logo">
        </a>
    </div>
    <div class="menu"  style="font-size: 11px;">
        <nav>
            <ul>
                <li><a href="admin_home.php">Home</a></li>
                <li><a href="manage_products.php">Manage Products</a></li>
                <li><a href="manage_sellers.php">Manage Sellers</a></li>
                <li><a href="manage_buyers.php">Manage Buyers</a></li>
                <li><a href="manage_orders.php">View Orders</a></li>
                <li><a href="admin_sales_report.php">Sales Report</a></li>
            </ul>
        </nav>
    </div>
    
    <div class="welcome-message">
        <img src="../images/user_icon.png" width="20px" height="20px">
        <span><?php echo htmlspecialchars($admin_name); ?></span>
        <a href="admin_logout.php"><button class="logout-btn">Logout</button></a>
    </div>
</header>