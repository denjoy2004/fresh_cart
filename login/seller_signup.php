<?php
// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize user input
    $seller_name = mysqli_real_escape_string($conn, $_POST['seller-name']);
    $email = mysqli_real_escape_string($conn, $_POST['seller-email']);
    $mobile_no = mysqli_real_escape_string($conn, $_POST['seller-mbno']);
    $password = mysqli_real_escape_string($conn, $_POST['seller_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['seller_password_confirm']);
    $business_name = mysqli_real_escape_string($conn, $_POST['business-name']);

    $seller_area = mysqli_real_escape_string($conn, $_POST['shop-area']);
    $seller_city = mysqli_real_escape_string($conn, $_POST['shop-city']);
    $seller_state = mysqli_real_escape_string($conn, $_POST['shop-state']);
    $seller_pincode = mysqli_real_escape_string($conn, $_POST['shop-pincode']);

    // Validate that passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the user already exists
        $sql_check = "SELECT * FROM seller_table WHERE seller_username = ?";
        $stmt = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error_message = "Email already registered.";
        } else {
            // Insert new seller data into the database using prepared statements
            $sql_insert = "INSERT INTO seller_table (seller_name, seller_username, seller_mbno, seller_password, business_name, seller_area, seller_city, seller_state, seller_pincode)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $sql_insert);
            mysqli_stmt_bind_param($stmt_insert, 'sssssssss', $seller_name, $business_name, $mobile_no, $email, $hashed_password, $seller_area, $seller_city, $seller_state, $seller_pincode);

            if (mysqli_stmt_execute($stmt_insert)) {
                // Redirect on successful signup
                header("Location: /seller/seller_home.php");
                exit();
            } else {
                $error_message = "Error: " . mysqli_error($conn);
            }
        }
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
    <title>Fresh Cart - Seller Sign Up</title>
    <link rel="stylesheet" href="signup.css">
    <style>
        .error-message {
            color: red;
            margin-top: 5px;
            font-size: 0.9em;
            margin-right: 120px;
        }
    </style>
    <script>
        function validate_signup() {
            const password = document.getElementById("seller_password").value;
            const confirmPassword = document.getElementById("seller_password_confirm").value;

            // Check if passwords match
            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }
    </script>
</head>
<body>
    <div class="container">    
        <header>
            <div class="logo">
                <a href="index.html"><img src="/fresh_cart/images/logo-no-background.png" width="200px" height="auto"></a>
            </div>
        </header>     
        <div class="page1-img">
            <img src="/fresh_cart/images/login_select.jpg" width="100%" height="50%">
        </div>
    </div>

    <div class="signup">
        <h2>Seller Sign Up</h2>
        <div class="seller_details">
            <form name="seller_signup" action="seller_signup.php" method="post" onsubmit="return validate_signup()">
                <div class="input-field">
                    <input type="text" id="seller-name" name="seller-name" placeholder="Seller Name" required>
                </div>
                <div class="input-field">
                    <input type="email" id="seller-email" name="seller-email" placeholder="Email" required>
                    <?php
                    if (isset($error_message)) {
                        echo "<span class='error-message'>" . htmlspecialchars($error_message) . "</span>";
                    }
                    ?>
                </div>
                <div class="input-field">
                    <input type="text" id="seller-mbno" name="seller-mbno" placeholder="Mobile Number" required>
                </div>
                <div class="input-field">
                    <input type="password" id="seller_password" name="seller_password" placeholder="Password" required>
                </div>
                <div class="input-field">
                    <input type="password" id="seller_password_confirm" name="seller_password_confirm" placeholder="Confirm Password" required>
                </div>
            </div>

            <div class="address">
                 <div class="input-field">
                    <input type="text" id="business-name" name="business-name" placeholder="Business Name" required>
                </div>
                <div class="input-field">
                    <input type="text" id="shop-area" name="shop-area" placeholder="Area" required>
                </div>
                <div class="input-field">
                    <input type="text" id="shop-city" name="shop-city" placeholder="City" required>
                </div>
                <div class="input-field">
                    <input type="text" id="shop-state" name="shop-state" placeholder="State" required>
                </div>
                <div class="input-field">
                    <input type="text" id="shop-pincode" name="shop-pincode" placeholder="PinCode" required>
                </div>
            </div>
            <button type="submit">Sign Up</button>
        </form>
    </div>

    <section class="contact-info">
        <h2>Contact Information</h2>
        <address>
            Fresh Cart<br>
            Kochi<br>
            Kerala, 686582<br>
            Phone: <a href="tel:9539658310">+91 9539658310</a><br>
            Email: <a href="mailto:freshcart@gmail.com">freshcart@gmail.com</a>
        </address>
    </section>
    
    <footer>&copy; Copyright 2024 Fresh Cart. All rights reserved.</footer>
</body>
</html>
