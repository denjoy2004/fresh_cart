<?php
// Start the session
session_start();

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
    $name = mysqli_real_escape_string($conn, $_POST['buyer_name']);
    $mobile_no = mysqli_real_escape_string($conn, $_POST['buyer_mbno']);
    $username = mysqli_real_escape_string($conn, $_POST['buyer_username']);
    $password = mysqli_real_escape_string($conn, $_POST['buyer_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['buyer_password_confirm']);
    $house_name = mysqli_real_escape_string($conn, $_POST['buyer_house_name']);
    $area = mysqli_real_escape_string($conn, $_POST['buyer_area']);
    $city = mysqli_real_escape_string($conn, $_POST['buyer_city']);
    $state = mysqli_real_escape_string($conn, $_POST['buyer_state']);
    $pincode = mysqli_real_escape_string($conn, $_POST['buyer_pincode']);

    // Validate that passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match!";
    } else {
        // Check if the user already exists
        $sql_check = "SELECT * FROM buyer_table WHERE buyer_username = ?";
        $stmt = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error_message = "Email already registered.";
        } else {
            // Insert new user data into the database using prepared statements
            $sql_insert = "INSERT INTO buyer_table (buyer_name, buyer_mbno, buyer_username, buyer_password, buyer_house_name, buyer_area, buyer_city, buyer_state, buyer_pincode)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $sql_insert);
            mysqli_stmt_bind_param($stmt_insert, 'sssssssss', $name, $mobile_no, $username, $password, $house_name, $area, $city, $state, $pincode);

            if (mysqli_stmt_execute($stmt_insert)) {
                // Set session variables
                $_SESSION['buyer_username'] = $username;
                $_SESSION['buyer_name'] = $name;

                // Redirect on successful signup
                header("Location: buyer_home.php");
                exit(); // Ensure script stops execution after redirection
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
    <title>Fresh Cart - Buyer Sign Up</title>
    <link rel="stylesheet" href="../login/signup.css">
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
            const password = document.getElementById("buyer_password").value;
            const confirmPassword = document.getElementById("buyer_password_confirm").value;

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
        <h2>Sign Up</h2>
        <div class="personal_details">
            <form name="user_signup" action="buyer_signup.php" method="post" onsubmit="return validate_signup()">
                <div class="input-field">
                    <input type="text" id="buyer_name" name="buyer_name" placeholder="Name" required>
                </div>
                <div class="input-field">
                    <input type="text" id="buyer_mbno" name="buyer_mbno" placeholder="Mobile Number" required>
                </div>
                <div class="input-field">
                    <input type="email" id="buyer_username" name="buyer_username" placeholder="Email" required>
                    <?php
                    if (isset($error_message)) {
                        echo "<span class='error-message'>" . htmlspecialchars($error_message) . "</span>";
                    }
                    ?>
                </div>
                <div class="input-field">
                    <input type="password" id="buyer_password" name="buyer_password" placeholder="Password" required>
                </div>
                <div class="input-field">
                    <input type="password" id="buyer_password_confirm" name="buyer_password_confirm" placeholder="Confirm Password" required>
                </div>            
            </div>

            <div class="address">
                <div class="input-field">
                    <input type="text" id="buyer_house_name" name="buyer_house_name" placeholder="House Name">
                </div>
                <div class="input-field">
                    <input type="text" id="buyer_area" name="buyer_area" placeholder="Area" required>
                </div>
                <div class="input-field">
                    <input type="text" id="buyer_city" name="buyer_city" placeholder="City" required>
                </div>
                <div class="input-field">
                    <input type="text" id="buyer_state" name="buyer_state" placeholder="State" required>
                </div>
                <div class="input-field">
                    <input type="text" id="buyer_pincode" name="buyer_pincode" placeholder="PinCode" required>
                </div>
            </div>
            <button type="submit">Sign Up</button>
        </form>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>
