

<!DOCTYPE html>
<html lang="en">
<h>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Cart - Buyer Sign Up</title>
    <link rel="stylesheet" href="signup.css">
    <style>
        .error-message {
            color: red;
            margin-top: 5px;
            font-size: 0.9em;
            margin-right: 120px;
        }
    </style>
    <script src="/login/login.js"></script>
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
