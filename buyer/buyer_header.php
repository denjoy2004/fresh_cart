<?php
// Start the session only if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the buyer is logged in
if (!isset($_SESSION['buyer_username'])) {
    // Redirect to the login page if not logged in
    header("Location: buyer_login.php");
    exit();
}

// Get the buyer's name from the session
$buyer_name = $_SESSION['buyer_name'];
?>
<!-- Font Awesome Kit for Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="../css/header.css">

<header>
    <div class="logo">
        <a href="buyer_home.php">
            <img src="../images/logo-no-background.png" width="200px" alt="Fresh Cart Logo">
        </a>
    </div>
    <div class="menu">
        <nav>
            <ul>
                <li><a href="buyer_home.php">Home</a></li>
                <li><a href="products_list.php">Products</a></li>
                <li><a href="my_orders.php">My Orders</a></li>
                <li><a href="update_buyer_account.php">Update Account</a></li>
                <li><a href="cart.php"><i class="fa fa-shopping-cart" style="font-size:36px"></i></a></li>
            </ul>
        </nav>
    </div>
        
    <div class="welcome-message">
        <img src="../images/user_icon.png" width="20px" height="20px">
        <span><?php echo htmlspecialchars($buyer_name); ?></span>
    </div>

    <a href="buyer_logout.php"><button class="logout-btn">Logout</button></a>
</header>
