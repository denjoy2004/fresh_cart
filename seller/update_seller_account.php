<?php
session_start();
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Include your database connection file

// Check if the seller is logged in
if (!isset($_SESSION['seller_username'])) {
    header("Location: seller_login.php");
    exit();
}

// Initialize variables
$seller_username = $_SESSION['seller_username'];
$error = "";
$success = "";

// Fetch seller data from the database
$query = "SELECT seller_name, seller_mbno, seller_password, business_name, seller_area, seller_city, seller_state, seller_pincode
          FROM seller_table
          WHERE seller_username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $seller_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $sellerData = $result->fetch_assoc();
} else {
    $error = "Unable to fetch seller details.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seller_name = $_POST['seller_name'];
    $seller_mbno = $_POST['seller_mbno'];
    $seller_password = $_POST['seller_password'];
    $business_name = $_POST['business_name'];
    $seller_area = $_POST['seller_area'];
    $seller_city = $_POST['seller_city'];
    $seller_state = $_POST['seller_state'];
    $seller_pincode = $_POST['seller_pincode'];

    // Validate input (you can add more validations as required)
    if (empty($seller_name) || empty($seller_mbno) || empty($seller_password) || empty($business_name)) {
        $error = "All fields are required.";
    } else {
        // Update seller details in the database
        $updateQuery = "UPDATE seller_table
                        SET seller_name = ?, seller_mbno = ?, seller_password = ?, business_name = ?, 
                            seller_area = ?, seller_city = ?, seller_state = ?, seller_pincode = ?
                        WHERE seller_username = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('sssssssss', $seller_name, $seller_mbno, $seller_password, $business_name,
                          $seller_area, $seller_city, $seller_state, $seller_pincode, $seller_username);
        if ($stmt->execute()) {
            $success = "Account details updated successfully.";
        } else {
            $error = "Failed to update details. Please try again.";
        }
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Seller Account - Fresh Cart</title>
    <link rel="stylesheet" href="/fresh_cart/css/update_seller_account.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            <button class="logout-btn" onclick="window.location.href='seller_logout.php'">Logout</button>
        </header>
        
        <div class="form-container">
            <h2>Update Account Information</h2>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <label for="seller_name">Seller Name:</label>
                <input type="text" id="seller_name" name="seller_name" value="<?php echo htmlspecialchars($sellerData['seller_name']); ?>" required>

                <label for="seller_mbno">Mobile Number:</label>
                <input type="text" id="seller_mbno" name="seller_mbno" value="<?php echo htmlspecialchars($sellerData['seller_mbno']); ?>" required>

                <label for="seller_password">Password:</label>
                <div class="password-wrapper">
                    <input type="password" id="seller_password" name="seller_password" value="<?php echo htmlspecialchars($sellerData['seller_password']); ?>" required>
                    <i class="far fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                </div>

                <script>
                    const togglePassword = document.querySelector('#togglePassword');
                    const passwordField = document.querySelector('#seller_password');
                    togglePassword.addEventListener('click', function () {
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);
                    this.classList.toggle('fa-eye-slash');
                    });
                </script>

                <label for="business_name">Business Name:</label>
                <input type="text" id="business_name" name="business_name" value="<?php echo htmlspecialchars($sellerData['business_name']); ?>" required>
            
                <label for="seller_area">Area:</label>
                <input type="text" id="seller_area" name="seller_area" value="<?php echo htmlspecialchars($sellerData['seller_area']); ?>" required>

                <label for="seller_city">City:</label>
                <input type="text" id="seller_city" name="seller_city" value="<?php echo htmlspecialchars($sellerData['seller_city']); ?>" required>

                <label for="seller_state">State:</label>
                <input type="text" id="seller_state" name="seller_state" value="<?php echo htmlspecialchars($sellerData['seller_state']); ?>" required>

                <label for="seller_pincode">Pincode:</label>
                <input type="text" id="seller_pincode" name="seller_pincode" value="<?php echo htmlspecialchars($sellerData['seller_pincode']); ?>" required>

                <button type="submit" class="update-btn">Update Account</button>
                <a href="seller_home.php"><button type="button" class="back-button">Back to Home</button></a>
            </form>
        </div>
    </div>
</body>
</html>
