document.addEventListener('DOMContentLoaded', () => {
    const openPopupBtn = document.getElementById('openPopupBtn');
    const popup = document.getElementById('popup');
    const closePopupBtn = document.getElementById('closePopupBtn');
    const userTypeButtons = document.querySelectorAll('.user-type-btn');

    openPopupBtn.addEventListener('click', () => {
        popup.style.display = 'block';
    });

    closePopupBtn.addEventListener('click', () => {
        popup.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target === popup) {
            popup.style.display = 'none';
        }
    });

    userTypeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const userType = button.getAttribute('data-type');
            let url = '';

            switch (userType) {
                case 'user':
                    url = 'buyer/buyer_login.php';
                    break;
                case 'seller':
                    url = 'seller/seller_login.php';
                    break;
                case 'admin':
                    url = 'admin/admin_login.php';
                    break;
                default:
                    break;
            }

            if (url) {
                window.location.href = url;
            }
        });
    });
});

function validate_login(){
    const username=document.getElementById("username").value;
    const password=document.getElementById("userpassword").value;
    
   
    if (password==null || password==""){  
        alert("Name can't be blank");  
        return false;  
    }
}

function validate_buyer_signup() {
    const name = document.getElementById("buyer_name").value.trim();
    const mobileNo = document.getElementById("buyer_mbno").value.trim();
    const username = document.getElementById("buyer_username").value.trim();
    const password = document.getElementById("buyer_password").value.trim();
    const confirmPassword = document.getElementById("buyer_password_confirm").value.trim();
    const houseName = document.getElementById("buyer_house_name").value.trim();
    const area = document.getElementById("buyer_area").value.trim();
    const city = document.getElementById("buyer_city").value.trim();
    const state = document.getElementById("buyer_state").value.trim();
    const pincode = document.getElementById("buyer_pincode").value.trim();

    // Check if all fields are filled
    if (name === "" || mobileNo === "" || username === "" || password === "" || confirmPassword === "" || area === "" || city === "" || state === "" || pincode === "") {
        alert("Please fill all the fields.");
        return false; // Prevent form submission
    }

    // Check if passwords match
    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return false; // Prevent form submission
    }

    // Check if mobile number is valid
    if (!/^[0-9]{10}$/.test(mobileNo)) {
        alert("Invalid mobile number. Please enter a 10-digit number.");
        return false; // Prevent form submission
    }

    // Check if pincode is valid
    if (!/^[0-9]{6}$/.test(pincode)) {
        alert("Invalid pincode. Please enter a 6-digit number.");
        return false; // Prevent form submission
    }

    // Check if email is valid
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(username)) {
        alert("Invalid email address.");
        return false; // Prevent form submission
    }

    // Check if password is strong enough
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!passwordRegex.test(password)) {
        alert("Password should be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.");
        return false; // Prevent form submission
    }

    return true; // Allow form submission
}
function validate_seller_signup() {
    const sellerName = document.getElementById("seller-name").value.trim();
    const email = document.getElementById("seller-email").value.trim();
    const mobileNo = document.getElementById("seller-mbno").value.trim();
    const password = document.getElementById("seller_password").value.trim();
    const confirmPassword = document.getElementById("seller_password_confirm").value.trim();
    const businessName = document.getElementById("business-name").value.trim();
    const sellerArea = document.getElementById("shop-area").value.trim();
    const sellerCity = document.getElementById("shop-city").value.trim();
    const sellerState = document.getElementById("shop-state").value.trim();
    const sellerPincode = document.getElementById("shop-pincode").value.trim();

    // Check if all fields are filled
    if (sellerName === "" || email === "" || mobileNo === "" || password === "" || confirmPassword === "" || businessName === "" || sellerArea === "" || sellerCity === "" || sellerState === "" || sellerPincode === "") {
        alert("Please fill all the fields.");
        return false; // Prevent form submission
    }

    // Check if passwords match
    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return false; // Prevent form submission
    }

    // Check if mobile number is valid
    if (!/^[0-9]{10}$/.test(mobileNo)) {
        alert("Invalid mobile number. Please enter a 10-digit number.");
        return false; // Prevent form submission
    }

    // Check if pincode is valid
    if (!/^[0-9]{6}$/.test(sellerPincode)) {
        alert("Invalid pincode. Please enter a 6-digit number.");
        return false; // Prevent form submission
    }

    // Check if email is valid
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(email)) {
        alert("Invalid email address.");
        return false; // Prevent form submission
    }

    // Check if password is strong enough
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!passwordRegex.test(password)) {
        alert("Password should be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.");
        return false; // Prevent form submission
    }

    return true; // Allow form submission
}