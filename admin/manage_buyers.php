<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

include '../db_connection.php'; // Adjust the path as necessary

// Fetch all active buyers
$buyer_query = "SELECT buyer_username, buyer_name, buyer_mbno, buyer_house_name, buyer_area, buyer_city, buyer_state, buyer_pincode, status FROM buyer_table WHERE status = 'active'";
$buyers_result = $conn->query($buyer_query);

// Handle account removal (marking as removed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!empty($_POST['buyer_username'])) {
        $buyer_username = $_POST['buyer_username'];

        // Update the buyer's status to 'removed'
        $update_query = "UPDATE buyer_table SET status = 'removed' WHERE buyer_username = ?";
        $stmt = $conn->prepare($update_query);
        if ($stmt) {
            $stmt->bind_param('s', $buyer_username);
            if ($stmt->execute()) {
                // Successful update
                $_SESSION['success_message'] = "Buyer account removed successfully.";
            } else {
                $_SESSION['error_message'] = "Failed to remove the buyer account.";
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Failed to prepare the SQL statement.";
        }
    } else {
        $_SESSION['error_message'] = "Invalid buyer username.";
    }
    // Redirect to refresh the page after processing
    header("Location: manage_buyers.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Buyers - Admin</title>
    <link rel="stylesheet" href="../css/manage_buyers.css">
</head>
<body>
    <div class="container">
        <?php include 'admin_header.php'; ?>

        <main class="main">
            <h1>Manage Buyers</h1>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message"><?php echo htmlspecialchars($_SESSION['success_message']); ?></div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error-message"><?php echo htmlspecialchars($_SESSION['error_message']); ?></div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <table class="buyer-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($buyers_result && $buyers_result->num_rows > 0): ?>
                        <?php while ($buyer = $buyers_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($buyer['buyer_username']); ?></td>
                                <td><?php echo htmlspecialchars($buyer['buyer_name']); ?></td>
                                <td><?php echo htmlspecialchars($buyer['buyer_mbno']); ?></td>
                                <td>
                                    <?php
                                        echo htmlspecialchars($buyer['buyer_house_name']) . ", " .
                                             htmlspecialchars($buyer['buyer_area']) . ", " .
                                             htmlspecialchars($buyer['buyer_city']) . ", " .
                                             htmlspecialchars($buyer['buyer_state']) . " - " .
                                             htmlspecialchars($buyer['buyer_pincode']);
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($buyer['status']); ?></td>
                                <td>
                                    <form action="view_buyer.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="buyer_username" value="<?php echo htmlspecialchars($buyer['buyer_username']); ?>">
                                        <input type="hidden" name="action" value="view">
                                        <button type="submit" class="view">View</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="manage_buyers.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="buyer_username" value="<?php echo htmlspecialchars ($buyer['buyer_username']); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="remove_btn">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No active buyers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php include '../footer.php'; ?>
        </main>
    </div>
</body>
</html>