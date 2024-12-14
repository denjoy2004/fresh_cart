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

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize user input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Check if the admin exists
    $sql = "SELECT * FROM admin_table WHERE admin_username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Check the password directly (no hashing)
        if ($password === $row['admin_password']) {
            // Store admin info in session and redirect to admin home
            $_SESSION['admin_username'] = $row['admin_username'];
            $_SESSION['admin_name'] = $row['admin_name']; // Ensure this is set

            // Redirect to admin home
            header("Location: admin_home.php");
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "Username not found.";
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
    <title>Fresh Cart - Admin Login</title>
    <link rel="stylesheet" href="../login/login.css">
    <script src="../js/login.js"></script>
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
        <form action="admin_login.php" method="post" onsubmit="return validate_login()">
            <h2>Admin Login</h2>
            <div class="input-field">
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>

            <div class="input-field">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit">Log In</button>

            <!-- Display error message if login fails -->
            <?php if (isset($error_message)): ?>
                <span class="error-message"><?php echo htmlspecialchars($error_message); ?></span>
            <?php endif; ?>
        </form>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>