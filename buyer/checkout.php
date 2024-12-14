<?php
session_start();
if (!isset($_SESSION['buyer_username'])) {
    header("Location: buyer_login.php");
    exit();
}

// Include database connection
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php';

$buyer_id = $_SESSION['buyer_username'];
$total_price = 0;
$cart_items = [];

// Fetch buyer details
$buyer_sql = "SELECT buyer_name, buyer_house_name, buyer_area, buyer_city, buyer_state, buyer_pincode, buyer_mbno 
              FROM buyer_table 
              WHERE buyer_username = ?";
$buyer_stmt = $conn->prepare($buyer_sql);
$buyer_stmt->bind_param("s", $buyer_id);
$buyer_stmt->execute();
$buyer_result = $buyer_stmt->get_result();
$buyer_data = $buyer_result->fetch_assoc();

// Fetch items from the cart_table for the buyer
$cart_sql = "SELECT c.product_id, c.quantity, p.product_name, p.price, p.image_path, s.seller_name, p.description, p.min_quantity
             FROM cart_table AS c 
             JOIN product_table AS p ON c.product_id = p.product_id 
             JOIN seller_table AS s ON p.seller_id = s.seller_username 
             WHERE c.buyer_id = ?";
$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("s", $buyer_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

// Check if cart is empty
if ($cart_result->num_rows === 0) {
    echo "<p>Your cart is empty. Please add items to your cart.</p>";
    exit();
}

while ($cart_row = $cart_result->fetch_assoc()) {
    // Calculate the subtotal
    $subtotal = $cart_row['price'] * $cart_row['quantity'];
    $total_price += $subtotal;

    $cart_items[] = [
        'product_name' => $cart_row['product_name'],
        'quantity' => $cart_row['quantity'],
        'price' => $cart_row['price'],
        'subtotal' => $subtotal,
        'product_id' => $cart_row['product_id'],
        'image_path' => $cart_row['image_path'],
        'seller_name' => $cart_row['seller_name'],
        'description' => $cart_row['description'],
        'min_quantity' => $cart_row['min_quantity']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../css/checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<div class="container">
    
    <?php include 'buyer_header.php'; ?>

    <h1>Checkout</h1>

    <h2>Order Summary</h2>
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Product</th>
                <th>Seller</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th> 
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><img src="../uploads/<?php echo htmlspecialchars($item['image_path']); ?>" alt="Product Image"></td>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['seller_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['min_quantity']); ?> * <?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>&#8377; <?php echo number_format($item['price'], 2); ?></td>
                    <td>&#8377; <?php echo number_format($item['subtotal'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">Total:</td>
                <td>&#8377; <?php echo number_format($total_price, 2); ?></td>
            </tr>
        </tfoot>
    </table>

    <h2>Shipping Address and Contact Details</h2>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($buyer_data['buyer_name']); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($buyer_data['buyer_house_name'] . ", " . $buyer_data['buyer_area'] . ", " . $buyer_data['buyer_city'] . ", " . $buyer_data['buyer_state'] . " - " . $buyer_data['buyer_pincode']); ?></p>
    <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($buyer_data['buyer_mbno']); ?></p>

    <h2>Payment Options</h2>
    <form action="order_successful.php" method="POST">
        <input type="hidden" name="buyer_username" value="<?php echo htmlspecialchars($buyer_id); ?>">
        <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">

        <label>
            <input type="radio" name="payment_method" value="upi" required> UPI
        </label><br>
        <label>
            <input type="radio" name="payment_method" value="credit_debit_card" required> Credit Card / Debit Card
        </label><br>
        <label>
            <input type="radio" name="payment_method" value="cash_on_delivery" required> Cash on Delivery
        </label><br>

        <div class="proceed-btn-container">
            <button type="submit" class="proceed-btn">Proceed to Payment</button>
        </div>
    </form>

    <?php include '../footer.php'; ?>

</div>

<script>
    // No need for JavaScript to show/hide card details since they are removed
</script>

</body>
</html>
