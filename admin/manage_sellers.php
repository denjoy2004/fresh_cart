<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

include '../db_connection.php'; // Adjust the path as necessary

// Fetch all active sellers
$seller_query = "SELECT seller_username, seller_name, seller_mbno, business_name, seller_area, seller_city, seller_state, seller_pincode,status FROM seller_table WHERE status = 'active'";
$sellers_result = $conn->query($seller_query);

// Handle account removal (marking as removed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!empty($_POST['seller_username'])) {
        $seller_username = $_POST['seller_username'];

        // Update the seller's status to 'removed'
        $update_seller_query = "UPDATE seller_table SET status = 'removed' WHERE seller_username = ?";
        $stmt = $conn->prepare($update_seller_query);
        if ($stmt) {
            $stmt->bind_param('s', $seller_username);
            if ($stmt->execute()) {
                // Successful update for seller
                $_SESSION['success_message'] = "Seller account removed successfully.";

                // Now update the products associated with this seller
                $update_products_query = "UPDATE product_table SET status = 'removed' WHERE seller_id = ?";
                $product_stmt = $conn->prepare($update_products_query);
                if ($product_stmt) {
                    $product_stmt->bind_param('s', $seller_username);
                    $product_stmt->execute();
                    $product_stmt->close();
                }
            } else {
                $_SESSION['error_message'] = "Failed to remove the seller account.";
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Failed to prepare the SQL statement.";
        }
    } else {
        $_SESSION['error_message'] = "Invalid seller username.";
    }
    // Redirect to refresh the page after processing
    header("Location: manage_sellers.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sellers - Admin</title>
    <link rel="stylesheet" href="../css/manage_buyers.css">
</head>
<body>
    <div class="container">
        <?php include 'admin_header.php'; ?>

        <main class="main">
            <h1>Manage Sellers</h1>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message"><?php echo htmlspecialchars($_SESSION['success_message']); ?></div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error-message"><?php echo htmlspecialchars($_SESSION['error_message']); ?></div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <table class="seller-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Business Name</th>
                        <th>Address</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sellers_result && $sellers_result->num_rows > 0): ?>
                        <?php while ($seller = $sellers_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($seller['seller_username']); ?></td>
                                <td><?php echo htmlspecialchars($seller['seller_name']); ?></td>
                                <td><?php echo htmlspecialchars($seller['seller_mbno']); ?></td>
                                <td><?php echo htmlspecialchars($seller['business_name']); ?></td>
                                <td>
                                    <?php
                                        echo htmlspecialchars($seller['seller_area']) . ", " .
                                             htmlspecialchars($seller['seller_city']) . ", " .
                                             htmlspecialchars($seller['seller_state']) . " - " . // Fixed typo here
                                             htmlspecialchars($seller['seller_pincode']);
                                    ?>
                                </td>
                                <td>
                                    <form action="view_seller_products.php " method="POST" style="display:inline;">
                                        <input type="hidden" name="seller_username" value="<?php echo htmlspecialchars($seller['seller_username']); ?>">
                                        <input type="hidden" name="action" value="view">
                                        <button type="submit" class="view">View</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="manage_sellers.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="seller_username" value="<?php echo htmlspecialchars($seller['seller_username']); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="remove_btn">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No active sellers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php include '../footer.php'; ?>
        </main>
    </div>
</body>
</html>