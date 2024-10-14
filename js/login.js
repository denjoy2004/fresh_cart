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
                    url = 'admin.html';
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

function validate_signup() {
    const username=document.getElementById("user-name").value;
    const phoneNumber=document.getElementById("user-mbno").value;
    const email=document.getElementById("user-email").value;
    const password=document.getElementById("user_password").value;
    const confirmPassword=document.getElementById("user_password_confirm").value;
    const housename=document.getElementById("house-name").value;
    const area=document.getElementById("user-area").value;
    const city=document.getElementById("user-city").value;
    const state=document.getElementById("user-state").value;
    const pincode=document.getElementById("user-pincode").value;

    if (username==null || username==""){  
        alert("Name can't be blank");  
        return false;  
    }
    const phoneRegex = /^\d{10}$/;
    if (!phoneRegex.test(phoneNumber)) {
        alert("Please enter a valid 10-digit mobile number");
        return false;
    }  
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      alert("Please enter a valid email address.");
      return false;
    }
    if (password !== confirmPassword) {
      alert("Passwords do not match.");
      return false; 
    }
    if(!housename.trim()){
        alert("Please enter a valid house name.");
        return false;
    }
    if(!area.trim()){
        alert("Please enter a valid area.");
        return false;
    }
    if(!city.trim()){
        alert("Please enter a valid street name.");
        return false;
    }
    if(!state.trim()) {
        alert("Please enter a state.");
        return false;
    }
    if(!pincode.trim() || !/^\d{6}$/.test(pincode)){
        alert("Please enter a valid 6-digit pincode.");
        return false;
    }
    return true;
}   

