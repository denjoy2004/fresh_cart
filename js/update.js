function update_buyer() {
    let isValid = true;
    const buyerName = document.getElementById('buyer_name').value.trim();
    const buyerMbno = document.getElementById('buyer_mbno').value.trim();
    const buyerPassword = document.getElementById('buyer_password').value.trim();
    const buyerHouseName = document.getElementById('buyer_house_name').value.trim();
    const buyerArea = document.getElementById('buyer_area').value.trim();
    const buyerCity = document.getElementById('buyer_city').value.trim();
    const buyerState = document.getElementById('buyer_state').value.trim();
    const buyerPincode = document.getElementById('buyer_pincode').value.trim();

    // Validate Buyer Name
    if (buyerName === '') {
        alert('Buyer Name is required.');
        isValid = false;
    }

    // Validate Mobile Number
    if (buyerMbno === '' || !/^\d{10}$/.test(buyerMbno)) {
        alert('Mobile Number must be 10 digits.');
        isValid = false;
    }

    // Validate Password
    if (buyerPassword === '' || buyerPassword.length < 6) {
        alert('Password must be at least 6 characters long.');
        isValid = false;
    }

    // Validate House Name
    if (buyerHouseName === '') {
        alert('House Name is required.');
        isValid = false;
    }

    // Validate Area
    if (buyerArea === '') {
        alert('Area is required.');
        isValid = false;
    }

    // Validate City
    if (buyerCity === '') {
        alert('City is required.');
        isValid = false;
    }

    // Validate State
    if (buyerState === '') {
        alert('State is required.');
        isValid = false;
    }

    // Validate Pincode
    if (buyerPincode === '' || !/^\d{6}$/.test(buyerPincode)) {
        alert('Pincode must be 6 digits.');
        isValid = false;
    }

    return isValid;
}

document.querySelector('form').addEventListener('submit', function (event) {
    if (!validateForm()) {
        event.preventDefault(); // Prevent form submission if validation fails
    }
});

function update_seller() {
    let isValid = true;

    // Get form field values
    const sellerName = document.getElementById('seller_name').value.trim();
    const sellerMbno = document.getElementById('seller_mbno').value.trim();
    const sellerPassword = document.getElementById('seller_password').value.trim();
    const businessName = document.getElementById('business_name').value.trim();
    const sellerArea = document.getElementById('seller_area').value.trim();
    const sellerCity = document.getElementById('seller_city').value.trim();
    const sellerState = document.getElementById('seller_state').value.trim();
    const sellerPincode = document.getElementById('seller_pincode').value.trim();

    // Validate Seller Name
    if (sellerName === '') {
        alert('Seller Name is required.');
        isValid = false;
    }

    // Validate Mobile Number
    if (sellerMbno === '' || !/^\d{10}$/.test(sellerMbno)) {
        alert('Mobile Number must be 10 digits.');
        isValid = false;
    }

    // Validate Password
    if (sellerPassword === '' || sellerPassword.length < 6) {
        alert('Password must be at least 6 characters long.');
        isValid = false;
    }

    // Validate Business Name
    if (businessName === '') {
        alert('Business Name is required.');
        isValid = false;
    }

    // Validate Area
    if (sellerArea === '') {
        alert('Area is required.');
        isValid = false;
    }

    // Validate City
    if (sellerCity === '') {
        alert('City is required.');
        isValid = false;
    }

    // Validate State
    if (sellerState === '') {
        alert('State is required.');
        isValid = false;
    }

    // Validate Pincode
    if (sellerPincode === '' || !/^\d{6}$/.test(sellerPincode)) {
        alert('Pincode must be 6 digits.');
        isValid = false;
    }

    return isValid;
}