<?php
session_start();
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Include your database connection file

// Initialize variables
$seller_username = $_SESSION['seller_username'] ?? null;
$totalSales = 0;
$totalAmount = 0;
$soldProducts = [];
$totalProductsSold = [];
$error = "";
$startDate = '';
$endDate = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';

    // Validate the date inputs
    if ($startDate && $endDate) {
        // Append the time to start and end date
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        // Query for total sales and total amount
        $query = "SELECT COUNT(*) AS total_sales, SUM(total_amount) AS total_amount
                  FROM order_table
                  WHERE seller_id = ? AND ordered_at BETWEEN ? AND ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sss', $seller_username, $startDateTime, $endDateTime);
        $stmt->execute();
        $stmt->bind_result($totalSales, $totalAmount);
        $stmt->fetch();
        $stmt->close(); // Close the statement after fetching results

        // Query for sold products with the quantity and date
        $query = "SELECT p.product_name, ot.quantity, ot.ordered_at
                  FROM order_table ot
                  JOIN product_table p ON ot.product_id = p.product_id
                  WHERE ot.seller_id = ? AND ot.ordered_at BETWEEN ? AND ?
                  ORDER BY ot.ordered_at ASC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sss', $seller_username, $startDateTime, $endDateTime);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $soldProducts[] = $row;
        }
        $stmt->close(); // Close the statement after fetching results

        // Query for total quantity sold and total amount for each product
        $query = "SELECT p.product_name, SUM(ot.quantity) AS total_quantity, SUM(ot.total_amount) AS total_amount
                  FROM order_table ot
                  JOIN product_table p ON ot.product_id = p.product_id
                  WHERE ot.seller_id = ? AND ot.ordered_at BETWEEN ? AND ?
                  GROUP BY p.product_name
                  ORDER BY total_quantity DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sss', $seller_username, $startDateTime, $endDateTime);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $totalProductsSold[] = $row;
        }
        $stmt->close();
    } else {
        $error = "Please enter both start and end dates.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Fresh Cart</title>
    <link rel="stylesheet" href="/fresh_cart/css/sales_report.css">
    <script>
        function logout() {
            window.location.href = 'seller_logout.php';  // Redirects to the PHP file
        }
    </script>
</head>
<body>
    <div class="container">    
        <header>
            <div class="logo">
                <img src="/fresh_cart/images/logo.png" alt="Fresh Cart Logo">
            </div>
            <div class="menu">
                <nav>
                    <ul>
                        <li><a href="seller_home.php">Home</a></li>
                        <li><a href="seller_products.php">My Products</a></li>
                        <li><a href="sales_report.php">Sales Report</a></li>
                    </ul>
                </nav>
            </div>
            <button class="logout-btn" onclick="logout()">Logout</button>
        </header>
        <div class="welcome">
            <h2>Sales Report</h2>
        </div>
        <form action="" method="POST">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" required>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" required>
            <button type="submit">Generate Report</button>
        </form>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="report-summary">
            <h3>Report Summary</h3>
            <p>Total Sales: <?php echo htmlspecialchars($totalSales); ?></p>
            <p>Total Amount: $<?php echo number_format($totalAmount, 2); ?></p>
        </div>

        <div class="sold-products">
            <h3>Sold Products</h3>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                    <th>Ordered At</th>
                </tr>
                <?php foreach ($soldProducts as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($product['ordered_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="total-products-sold">
            <h3>Total Products Sold</h3>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Total Quantity Sold</th>
                    <th>Total Amount</th>
                </tr>
                <?php foreach ($totalProductsSold as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($product['total_quantity']); ?></td>
                        <td>$<?php echo number_format($product['total_amount'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <button class="download-btn">Download Report</button>
    </div>
</body>
</html>
