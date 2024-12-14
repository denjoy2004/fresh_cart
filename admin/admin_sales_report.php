<?php
session_start();
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Adjust path as needed

// Initialize variables
$totalOrders = 0;
$totalRevenue = 0;
$orderStatusCounts = [];
$soldProducts = [];
$totalProductRevenue = 0;
$startDate = '';
$endDate = '';
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';

    // Validate date inputs
    if (!empty($startDate) && !empty($endDate)) {
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        // Query for total orders and revenue
        $query = "SELECT COUNT(*) AS total_orders, SUM(total_amount) AS total_revenue
                  FROM order_table
                  WHERE ordered_at BETWEEN ? AND ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $startDateTime, $endDateTime);
        $stmt->execute();
        $stmt->bind_result($totalOrders, $totalRevenue);
        $stmt->fetch();
        $stmt->close();

        // Query for order status counts
        $query = "SELECT order_status, COUNT(*) AS status_count
                  FROM order_table
                  WHERE ordered_at BETWEEN ? AND ?
                  GROUP BY order_status";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $startDateTime, $endDateTime);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $orderStatusCounts[$row['order_status']] = $row['status_count'];
        }
        $stmt->close();

        // Query for sold products with sorting by date and order ID, including buyer and seller details
        $query = "SELECT oi.order_item_id, oi.order_id, oi.product_id, oi.quantity, oi.price, oi.order_status, 
       p.product_name, SUM(oi.quantity) AS total_quantity, 
       SUM(oi.price * oi.quantity) AS total_amount,
       b.buyer_username, 
       CONCAT(b.buyer_house_name, ', ', b.buyer_area, ', ', b.buyer_city, ', ', b.buyer_state, ', ', b.buyer_pincode) AS buyer_address,
       b.buyer_mbno,
       s.seller_username, 
       CONCAT(s.seller_area, ', ', s.seller_city, ', ', s.seller_state, ', ', s.seller_pincode) AS seller_address,
       s.seller_mbno, o.order_id, o.total_amount, o.order_status, o.ordered_at
FROM order_items_table oi
JOIN product_table p ON oi.product_id = p.product_id
JOIN order_table o ON oi.order_id = o.order_id
JOIN buyer_table b ON oi.buyer_id = b.buyer_username
JOIN seller_table s ON oi.seller_id = s.seller_username
WHERE oi.ordered_at BETWEEN ? AND ?
GROUP BY oi.order_id, p.product_name, b.buyer_username, s.seller_username
ORDER BY o.ordered_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $startDateTime, $endDateTime);
        $stmt->execute();
        $result = $stmt->get_result();

        // Loop through the result and group products by order_id
        while ($row = $result->fetch_assoc()) {
            // Add the sold product to the array grouped by order_id
            $soldProducts[$row['order_id']]['order_id'] = $row['order_id'];
            $soldProducts[$row['order_id']]['ordered_at'] = $row['ordered_at'];
            $soldProducts[$row['order_id']]['total_amount'] = $row['total_amount'];
            $soldProducts[$row['order_id']]['buyer_username'] = $row['buyer_username'];
            $soldProducts[$row['order_id']]['buyer_address'] = $row['buyer_address'];
            $soldProducts[$row['order_id']]['buyer_mbno'] = $row['buyer_mbno'];
            $soldProducts[$row['order_id']]['order_status'] = $row['order_status'];

            // Append product details to each order's entry
            $soldProducts[$row['order_id']]['products'][] = [
                'product_name' => $row['product_name'],
                'product_id' => $row['product_id'],
                'quantity' => $row['total_quantity'],
                'price' => $row['price'],
                'total' => $row['total_amount'],
                'order_status' => $row['order_status']
            ];

            // Calculate total revenue for the product
            $totalProductRevenue += $row['total_amount'];
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
    <title>Admin Sales Report - Fresh Cart</title>
    <link rel="stylesheet" href="/fresh_cart/css/sales_report.css">
    <style>
        /* Custom Styles */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 8px;
        }
        header h1 {
            font-size: 28px;
            text-align: center;
            color: #4CAF50;
        }
        .date-form {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .date-form input, .date-form button {
            padding: 8px 12px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table thead {
            background-color: #4CAF50;
            color: white;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .nested-table {
            width: 100%;
            margin-top: 10px;
        }
        .nested-table th, .nested-table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
        }
        .total-revenue {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin Sales Report</h1>
        </header>
        
        <form class="date-form" action="" method="POST">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" required>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" required>
            <button type="submit">Generate Report</button>
        </form>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="sold-products">
            <h3>Sold Products (Sorted by Date & Order ID)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Buyer Username</th>
                        <th>Buyer Address</th>
                        <th>Buyer Mobile</th>
                        <th>Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($soldProducts as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars(date("Y-m-d", strtotime($order['ordered_at']))); ?></td>
                            <td>&#8377;<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($order['buyer_username']); ?></td>
                            <td><?php echo htmlspecialchars($order['buyer_address']); ?></td>
                            <td><?php echo htmlspecialchars($order['buyer_mbno']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                        </tr>
                        <tr class="nested-table">
                            <td colspan="8">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                            <th>Product Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order['products'] as $product): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                                <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                                                <td>&#8377;<?php echo number_format($product['price'], 2); ?></td>
                                                <td>&#8377;<?php echo number_format($product['total'], 2); ?></td>
                                                <td><?php echo htmlspecialchars($product['order_status']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Display Total Revenue -->
        <?php if (!empty($totalRevenue)): ?>
            <div class="total-revenue">
                <p>Total Revenue: &#8377;<?php echo number_format($totalRevenue, 2); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
