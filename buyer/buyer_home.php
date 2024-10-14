<?php
// Include database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Adjust the path as necessary

// Query to get the most sold products
$most_sold_query = "
    SELECT p.product_id, p.product_name, p.price, p.stock_quantity, p.image_path,
           COUNT(o.order_id) AS order_count
    FROM product_table p
    LEFT JOIN order_table o ON p.product_id = o.product_id
    GROUP BY p.product_id
    ORDER BY order_count DESC
    LIMIT 5;";

$most_sold_result = $conn->query($most_sold_query);

// Fetch promotions and testimonials (assuming these variables are already defined)
$promotions = ["20% off on all fruits!", "Buy 2 get 1 free on vegetables!"]; // Example promotions
$testimonials = ["Great service!", "Loved the fresh produce!"]; // Example testimonials

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Home - Fresh Cart</title>
    <link rel="stylesheet" href="../css/buyer_home.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="container">    
        <header>
            <div class="logo">
                <a href="index.html">
                    <img src="../images/logo-no-background.png" width="200px" height="auto" alt="Fresh Cart Logo">
                </a>
            </div>
            <div class="menu">
                <nav>
                    <ul>
                        <li><a href="products_list.php">Products</a></li>
                        <li><a href="my_orders.php">My Orders</a></li>
                        <li><a href="account_settings.php">Account Settings</a></li>
                        <li><a href="browse_products.php"><i class="fa fa-shopping-cart" style="font-size:36px"></i></a></li>
                    </ul>
                </nav>
            </div>
            <a href="buyer_logout.php"><button class="logout-btn">Logout</button></a>
        </header>
        
        <main>
            <!-- Most Sold Products Section -->
            <section class="most-sold">
                <h2>Most Sold Products</h2>
                <div class="product-grid">
                    <?php if ($most_sold_result->num_rows > 0): ?>
                        <?php while ($row = $most_sold_result->fetch_assoc()): ?>
                            <div class="product-card">
    <img src="../uploads/<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
    <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
    <p>Price: &#8377;<?php echo htmlspecialchars($row['price']); ?></p>
    <p>Stock: <?php echo htmlspecialchars($row['stock_quantity']); ?></p>
    <p>Orders Count: <?php echo htmlspecialchars($row['order_count']); ?></p>
    <div class="button-group">
        <button class="add-to-cart-btn">Add to Cart<i class="fa fa-shopping-cart"></i></button>
        <button class="buy-btn">Buy<i class="fa fa-shopping-cart"></i></button>
    </div>
</div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No sold products found.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Categories Section -->
            <section class="categories">
                <h2>Categories</h2>
                <div class="image-grid">
                    <div class="image-item">
                        <a href="category.php?slug=fruits">
                            <img src="../images/category1.jpg" alt="Fruits">
                        </a>
                    </div>
                    <div class="image-item">
                        <a href="category.php?slug=vegetables">
                            <img src="../images/category2.jpg" alt="Vegetables">
                        </a>
                    </div>
                    <div class="image-item">
                        <a href="category.php?slug=dairy">
                            <img src="../images/category3.jpg" alt="Dairy">
                        </a>
                    </div>
                    <div class="image-item">
                        <a href="category.php?slug=meat">
                            <img src="../images/category4.jpg" alt="Meat">
                        </a>
                    </div>
                    <div class="image-item">
                        <a href="category.php?slug=beverages">
                            <img src="../images/category5.jpg" alt="Beverages">
                        </a>
                    </div>
                </div>
            </section>
            <div class="about" id="about">
        <h1>About Us</h1>
        <p>
            "Fresh Cart" is an online marketplace dedicated to facilitating the buying and selling of fresh fruits and vegetables. Our platform offers a seamless experience for both sellers and buyers, providing a convenient avenue to access high-quality produce. With a user-friendly interface, customers can browse through a diverse range of fruits and vegetables sourced directly from local farmers and trusted suppliers. From seasonal favorites to exotic varieties, Fresh Cart ensures freshness and quality with every purchase. Whether you're a farmer looking to sell your harvest or a consumer seeking the finest produce, Fresh Cart is your go-to destination for all things fresh and delicious.
        </p>
    </div>

    <section class="contact-info" id="contact">
        <h2>Contact Information</h2>
        <address>
            Fresh Cart<br>
            Kochi<br>
            Kerala, 686582<br>
            Phone: <a href="tel:+919539658310">+91 9539658310</a><br>
            Email: <a href="mailto:freshcart@gmail.com">freshcart@gmail.com</a>
        </address>
    </section>
        </main>
    <footer>&copy; Copyright 2024 Fresh Cart. All rights reserved.</footer>
</body>
</html>
