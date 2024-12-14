<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Adjust the path as necessary

// Fetch all buyers
$buyer_query = "SELECT  buyer_username, buyer_name, buyer_mbno, buyer_house_name, buyer_area, buyer_city, buyer_state, buyer_pincode FROM buyer_table";
$buyers_result = $conn->query($buyer_query);

// Handle account removal (deletion)
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $buyer_username = $_POST['buyer_username'];

    // Delete the buyer account from the table
    $delete_query = "DELETE FROM buyer_table WHERE buyer_username = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('s', $buyer_username);  // Use 's' for string binding
    $stmt->execute();
    $stmt->close();

    // Redirect to refresh the page after deletion
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
        <header>
            <div class="logo">
                <a href="admin_home.php">
                    <img src="../images/logo-no-background.png" width="200px" height="auto" alt="Fresh Cart Logo">
                </a>
            </div>
            <div class="menu">
                <nav>
                    <ul>
                        <li><a href="admin_home.php">Dashboard</a></li>
                        <li><a href="manage_products.php">Manage Products</a></li>
                        <li><a href="manage_sellers.php">Manage Sellers</a></li>
                        <li><a href="manage_buyers.php">Manage Buyers</a></li>
                    </ul>
                </nav>
            </div>
            <button class="logout-btn">Logout</button>
        </header>

        <main class="main">
            <h1>Manage Buyers</h1>

            <table class="buyer-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($buyers_result->num_rows > 0): ?>
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
                                <td>
                                    <form action="view_buyer.php" method="GET" style="display:inline;">
                                        <input type="hidden" name="buyer_username" value="<?php echo $buyer['buyer_username']; ?>">
                                        <button type="submit" class="view">View</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="manage_buyers.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="buyer_username" value="<?php echo $buyer['buyer_username']; ?>">
                                        <button type="submit" class="remove_btn" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this buyer?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No buyers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php include '../footer.php'; ?>
        </main>
    </div>
</body>
</html>
