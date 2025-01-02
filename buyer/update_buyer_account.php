<?php
session_start();
include 'C:\xampp\htdocs\Fresh_Cart\db_connection.php'; // Include your database connection file

// Check if the buyer is logged in
if (!isset($_SESSION['buyer_username'])) {
    header("Location: buyer_login.php");
    exit();
}

// Initialize variables
$buyer_username = $_SESSION['buyer_username'];
$error = "";
$success = "";

// Fetch buyer data from the database
$query = "SELECT buyer_name, buyer_mbno, buyer_password, buyer_house_name, buyer_area, buyer_city, buyer_state, buyer_pincode
          FROM buyer_table
          WHERE buyer_username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $buyer_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $buyerData = $result->fetch_assoc();
} else {
    $error = "Unable to fetch buyer details.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buyer_name = $_POST['buyer_name'];
    $buyer_mbno = $_POST['buyer_mbno'];
    $buyer_password = $_POST['buyer_password'];
    $buyer_house_name = $_POST['buyer_house_name'];
    $buyer_area = $_POST['buyer_area'];
    $buyer_city = $_POST['buyer_city'];
    $buyer_state = $_POST['buyer_state'];
    $buyer_pincode = $_POST['buyer_pincode'];

    // Validate input (you can add more validations as required)
    if (empty($buyer_name) || empty($buyer_mbno) || empty($buyer_password) || empty($buyer_house_name)) {
        $error = "All fields are required.";
    } else {
        // Update buyer details in the database
        $updateQuery = "UPDATE buyer_table
                        SET buyer_name = ?, buyer_mbno = ?, buyer_password = ?, buyer_house_name = ?, 
                            buyer_area = ?, buyer_city = ?, buyer_state = ?, buyer_pincode = ?
                        WHERE buyer_username = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('sssssssss', $buyer_name, $buyer_mbno, $buyer_password, $buyer_house_name,
                          $buyer_area, $buyer_city, $buyer_state, $buyer_pincode, $buyer_username);
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
    <title>Update Buyer Account - Fresh Cart</title>
    <link rel="stylesheet" href="../css/update_buyer_account.css">
    <script src="../js/update.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <?php include 'buyer_header.php'; ?>
        <div class="form-container">
            <h2>Update Account Information</h2>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="" method="POST" onsubmit="return update_buyer()">
                <label for="buyer_name">Buyer Name:</label>
                <input type="text" id="buyer_name" name="buyer_name" value="<?php echo htmlspecialchars($buyerData['buyer_name']); ?>" required>

                <label for="buyer_mbno">Mobile Number:</label>
                <input type="text" id="buyer_mbno" name="buyer_mbno" value="<?php echo htmlspecialchars($buyerData['buyer_mbno']); ?>" required>

                <label for="buyer_password">Password:</label>
                <div class="password-wrapper">
                    <input type="password" id="buyer_password" name="buyer_password" value="<?php echo htmlspecialchars($buyerData['buyer_password']); ?>" required>
                    <i class="far fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                </div>

                <script>
                    const togglePassword = document.querySelector('#togglePassword');
                    const passwordField = document.querySelector('#buyer_password');
                    togglePassword.addEventListener('click', function () {
                        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordField.setAttribute('type', type);
                        this.classList.toggle('fa-eye-slash');
                    });
                </script>

                <label for="buyer_house_name">House Name:</label>
                <input type="text" id="buyer_house_name" name="buyer_house_name" value="<?php echo htmlspecialchars($buyerData['buyer_house_name']); ?>" required>
            
                <label for="buyer_area">Area:</label>
                <input type="text" id="buyer_area" name="buyer_area" value="<?php echo htmlspecialchars($buyerData['buyer_area']); ?>" required>

                <label for="buyer_city">City:</label>
                <input type="text" id="buyer_city" name="buyer_city" value="<?php echo htmlspecialchars($buyerData['buyer_city']); ?>" required>

                <label for="buyer_state">State:</label>
                <input type="text" id="buyer_state" name="buyer_state" value="<?php echo htmlspecialchars($buyerData['buyer_state']); ?>" required>

                <label for="buyer_pincode">Pincode:</label>
                <input type="text" id="buyer_pincode" name="buyer_pincode" value="<?php echo htmlspecialchars($buyerData['buyer_pincode']); ?>" required>

                <button type="submit" class="update-btn">Update Account</button>
                <a href="buyer_home.php"><button type="button" class="back-button">Back to Home</button></a>
            </form>
        </div>
        <?php include '../footer.php'; ?>

    </div>
</body>
</html>
