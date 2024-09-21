<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Cart</title>
    <link rel="stylesheet" href="signup.css">
    <style>
        .error-message {
            color: red;
            margin-top: 5px; /* Adds space between the input and error message */
            font-size: 0.9em; /* Smaller font for the error message */
            margin-right: 120px;
        }
    </style>
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
            <form name="user_signup" action="signup.php" method="post" onsubmit="return validate_signup()">
                <div class="input-field">
                    <input type="text" id="user-name" name="user-name" placeholder="Name" required>
                </div>
                <div class="input-field">
                    <input type="text" id="user-mbno" name="user-mbno" placeholder="Mobile Number" required>
                </div>
                <div class="input-field">
                    <input type="text" id="user-email" name="user-email" placeholder="Email" required>
                    <?php
                    // Check if there is an error related to the email
                    if (isset($_GET['error'])) {
                        echo "<span class='error-message'>" . htmlspecialchars($_GET['error']) . "</span>";
                    }
                    ?>
                </div>
                <div class="input-field">
                    <input type="password" id="user_password" name="user_password" placeholder="Password" required>
                </div>
                <div class="input-field">
                    <input type="password" id="user_password_confirm" name="user_password_confirm" placeholder="Confirm Password" required>
                </div>            
            </div>

            <div class="address">
                <div class="input-field">
                    <input type="text" id="house-name" name="house-name" placeholder="House Name">
                </div>
                <div class="input-field">
                    <input type="text" id="user-area" name="user-area" placeholder="Area" required>
                </div>
                <div class="input-field">
                    <input type="text" id="user-city" name="user-city" placeholder="City" required>
                </div>
                <div class="input-field">
                    <input type="text" id="user-state" name="user-state" placeholder="State" required>
                </div>
                <div class="input-field">
                    <input type="text" id="user-pincode" name="user-pincode" placeholder="PinCode" required>
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
