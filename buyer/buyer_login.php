<?php
// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Database connection
$server = "localhost";
$user = "root";
$pass = "";
$db = "fresh_cart";

$conn = mysqli_connect($server, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables
$error_message = '';

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize user input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        // Check if the user exists
        $sql = "SELECT * FROM buyer_table WHERE buyer_username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            
            // Verify the password
            if ($password === $row['buyer_password'] && $username === $row['buyer_username'] ) {
                // Store user info in session and redirect to buyer home
                $_SESSION['buyer_username'] = $row['buyer_username'];
                $_SESSION['buyer_name'] = $row['buyer_name'];
                header("Location: buyer_home.php");
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "Username not found.";
        }
    } else {
        $error_message = "Please enter both username and password.";
    }
}

// Close the connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Cart - Buyer Login</title>
    <link rel="stylesheet" href="../login/login.css">
    <script src="/login/login.js"></script>

</head>
<body>
    <div class="container">    
        <header>
            <div class="logo">
                <a href="index.html">
                    <img src="/fresh_cart/images/logo-no-background.png" width="200px" height="auto">
                </a>
            </div>
        </header>     
        <div class="page1-img">
            <img src="/fresh_cart/images/login_select.jpg" width="100%" height="50%">
        </div>
    </div>
    
    <div class="login-box">
        <form action="buyer_login.php" method="post">
            <h2>Buyer Login</h2>
            <div class="input-field">
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>

            <div class="input-field">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit">Log In</button>

            <!-- Display error message if login fails -->
            <?php if (!empty($error_message)): ?>
                <span class="error-message"><?php echo htmlspecialchars($error_message); ?></span>
            <?php endif; ?>

            <div class="register">
                <p>Don't have an account? <a href="buyer_signup.php">Register</a></p>
            </div>
        </form>
    </div>
    
    <?php include '../footer.php'; ?>
</body>
</html>
