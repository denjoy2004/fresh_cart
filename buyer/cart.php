<?php
session_start();
if (!isset($_SESSION['buyer_username'])) {
    header("Location: buyer_login.php");
    exit();
}

// Include database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Adjust the path if necessary

$buyer_id = $_SESSION['buyer_username']; // Assuming you store buyer_id in session

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    // Check if the product is already in the cart
    $check_sql = "SELECT * FROM cart_table WHERE buyer_id = ? AND product_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $buyer_id, $product_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update quantity if product already in cart
        $update_sql = "UPDATE cart_table SET quantity = quantity + ? WHERE buyer_id = ? AND product_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iii", $quantity, $buyer_id, $product_id);
        $update_stmt->execute();
    } else {
        // Add new product to cart
        $insert_sql = "INSERT INTO cart_table (buyer_id, product_id, quantity) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iii", $buyer_id, $product_id, $quantity);
        $insert_stmt->execute();
    }
}

// Handle Remove from Cart
if (isset($_POST['remove_from_cart'])) {
    $cart_id = $_POST['cart_id'];
    $remove_sql = "DELETE FROM cart_table WHERE cart_id = ? AND buyer_id= ?";
    $remove_stmt = $conn->prepare($remove_sql);
    $remove_stmt->bind_param("ii", $cart_id, $buyer_id);
    $remove_stmt->execute();
}

// Fetch cart items
$cart_sql = "SELECT c.cart_id, p.product_id, p.product_name, p.price, c.quantity, p.image_path
             FROM cart_table c 
             JOIN product_table p ON c.product_id = p.product_id 
             WHERE c.buyer_id = ?";
$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("i", $buyer_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Fresh Cart</title>
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="container">

    <?php include 'buyer_header.php'; ?>

        <main>
            <h1>Your Shopping Cart</h1>
            <table>
                <tr>
                    <th></th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th><b>Subtotal</b></th>
                    <th>Action</th>
                </tr>
                <?php while ($cart_item = $cart_result->fetch_assoc()) { ?>
                <tr>
                    <td><img src="../uploads/<?php echo htmlspecialchars($cart_item['image_path']); ?>" alt="<?php echo htmlspecialchars($cart_item['product_name']); ?>" style="width: 200px; height: 180px;"></td>
                    <td><?php echo htmlspecialchars($cart_item['product_name']); ?></td>
                    <td> &#8377; <?php echo htmlspecialchars($cart_item['price']); ?></td>
                    <td><?php echo htmlspecialchars($cart_item['quantity']); ?></td>
                    <td>&#8377; <?php echo htmlspecialchars($cart_item['price'] * $cart_item['quantity']); ?></td>
                    <td>
                        <form action="cart.php" method="post">
                            <input type="hidden" name="remove_from_cart" value="1">
                            <input type="hidden" name="cart_id" value="<?php echo htmlspecialchars($cart_item['cart_id']); ?>">
                            <button type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php $total_price += $cart_item['price'] * $cart_item['quantity']; } ?>
                <tr>
                    <td colspan="3">Total:</td>
                    <td>₹ <?php echo htmlspecialchars($total_price); ?></td>
                    <td></td>
                </tr>
            </table>

            <form action="checkout.php" method="post" class="checkout-form">
                <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">
                <input type="hidden" name="buyer_id" value="<?php echo htmlspecialchars($buyer_id); ?>">
                
                <?php
                // Fetch the cart items again to add them to the form
                $cart_result->data_seek(0); // Reset the result pointer
                while ($cart_item = $cart_result->fetch_assoc()) {
                ?>
                    <input type="hidden" name="product_id[]" value="<?php echo htmlspecialchars($cart_item['product_id']); ?>">
                    <input type="hidden" name="quantity[]" value="<?php echo htmlspecialchars($cart_item['quantity']); ?>">
                <?php } ?>
                
                <p>Total Amount: <strong>₹ <?php echo htmlspecialchars($total_price); ?></strong></p>
                <button type="submit" class="checkout-btn">Proceed to Checkout</button>
            </form>
        </main>
        <?php include '../footer.php'; ?>
    </div>
</body>
</html>
