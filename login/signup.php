<?php
$server = "localhost";
$user = "root";
$pass = "";
$db = "fresh_cart";

$conn = mysqli_connect($server, $user, $pass);
mysqli_select_db($conn, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['user-name'];
$mobile_no = $_POST['user-mbno'];
$username = $_POST['user-email'];
$password = $_POST['user_password'];
$house_name = $_POST['house-name'];
$area = $_POST['user-area'];
$city = $_POST['user-city'];
$state = $_POST['user-state'];
$pincode = $_POST['user-pincode'];

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql_check = "SELECT * FROM buyer_table WHERE username = '$username'";
$result = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result) > 0) {
    header("Location: user_signup.php?error=Email already registered.");
    exit();
} else {
    $sql_insert = "INSERT INTO buyer_table (name, mobile_no, username, password, house_name, area, city, state, pincode)
                   VALUES ('$name', '$mobile_no', '$username', '$hashed_password', '$house_name', '$area', '$city', '$state', '$pincode')";

    if (mysqli_query($conn, $sql_insert)) {
        header("Location : /buyer/buyer_home.php");
        exit();
    } else {
        echo "Error: " . $sql_insert . "<br>" . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
