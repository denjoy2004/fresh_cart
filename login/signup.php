<?php
// Database connection details
$server = "localhost";
$user = "root";
$pass = "";
$db = "fresh_cart";

// Create a connection
$conn = mysqli_connect($server, $user, $pass);
mysqli_select_db($conn, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$name = $_POST['user-name'];
$mobile_no = $_POST['user-mbno'];
$username = $_POST['user-email'];  // Assuming this is the email
$password = $_POST['user_password'];
$house_name = $_POST['house-name'];
$area = $_POST['user-area'];
$city = $_POST['user-city'];
$state = $_POST['user-state'];
$pincode = $_POST['user-pincode'];

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if the email already exists
$sql_check = "SELECT * FROM buyer_table WHERE username = '$username'";
$result = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result) > 0) {
    // If the email already exists, redirect back to signup.html with an error message
    header("Location: user_signup.php?error=Email already registered.");
    exit();  // Stop further execution of the script
} else {
    // If the email does not exist, proceed with the insertion
    $sql_insert = "INSERT INTO buyer_table (name, mobile_number, email, password, house_name, area, city, state, pincode)
                   VALUES ('$name', '$mbno', '$username', '$hashed_password', '$house_name', '$area', '$city', '$state', '$pincode')";

    if (mysqli_query($conn, $sql_insert)) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql_insert . "<br>" . mysqli_error($conn);
    }
}

// Close the connection
mysqli_close($conn);
?>
