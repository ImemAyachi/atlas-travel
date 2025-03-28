document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');
    const firstnameInput = document.getElementById('firstname');
    const lastnameInput = document.getElementById('lastname');
    const ageInput = document.getElementById('age');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const addressInput = document.getElementById('address');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const profileImageInput = document.getElementById('profile_image');
    const fileNameDisplay = document.getElementById('file-name');
    
    const firstnameError = document.getElementById('firstname-error');
    const lastnameError = document.getElementById('lastname-error');
    const ageError = document.getElementById('age-error');
    const emailError = document.getElementById('email-error');
    const phoneError = document.getElementById('phone-error');
    const addressError = document.getElementById('address-error');
    const passwordError = document.getElementById('password-error');
    const confirmPasswordError = document.getElementById('confirm-password-error');
    
    // Email validation pattern (RFC 5322 Official Standard)
    const emailPattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    
    // Phone validation pattern (basic)
    const phonePattern = /^\+?[0-9\s\-\(\)]{8,20}$/;
    
    // Show error message
    function showError(input, errorElement, message) {
        input.classList.add('input-error');
        input.classList.add('shake');
        errorElement.textContent = message;
        errorElement.classList.add('visible');
        
        setTimeout(() => {
            input.classList.remove('shake');
        }, 500);
    }
    
    // Clear error message
    function clearError(input, errorElement) {
        input.classList.remove('input-error');
        errorElement.textContent = '';
        errorElement.classList.remove('visible');
    }
    
    // Validate first name
    function validateFirstname() {
        const firstname = firstnameInput.value.trim();
        
        if (firstname === '') {
            showError(firstnameInput, firstnameError, 'First name is required');
            return false;
        } else if (firstname.length < 2) {
            showError(firstnameInput, firstnameError, 'First name must be at least 2 characters');
            return false;
        } else {
            clearError(firstnameInput, firstnameError);
            return true;
        }
    }
    
    // Validate last name
    function validateLastname() {
        const lastname = lastnameInput.value.trim();
        
        if (lastname === '') {
            showError(lastnameInput, lastnameError, 'Last name is required');
            return false;
        } else if (lastname.length < 2) {
            showError(lastnameInput, lastnameError, 'Last name must be at least 2 characters');
            return false;
        } else {
            clearError(lastnameInput, lastnameError);
            return true;
        }
    }
    
    // Validate age
    function validateAge() {
        const age = ageInput.value.trim();
        
        if (age === '') {
            showError(ageInput, ageError, 'Age is required');
            return false;
        } else if (!Number.isInteger(Number(age)) || Number(age) < 1) {
            showError(ageInput, ageError, 'Age must be a valid number');
            return false;
        } else {
            clearError(ageInput, ageError);
            return true;
        }
    }
    
    // Validate email
    function validateEmail() {
        const email = emailInput.value.trim();
        
        if (email === '') {
            showError(emailInput, emailError, 'Email is required');
            return false;
        } else if (!emailPattern.test(email)) {
            showError(emailInput, emailError, 'Please enter a valid email address');
            return false;
        } else {
            clearError(emailInput, emailError);
            return true;
        }
    }
    
    // Validate phone (optional)
    function validatePhone() {
        const phone = phoneInput.value.trim();
        
        if (phone !== '' && !phonePattern.test(phone)) {
            showError(phoneInput, phoneError, 'Please enter a valid phone number');
            return false;
        } else {
            clearError(phoneInput, phoneError);
            return true;
        }
    }
    
    // Validate address
    function validateAddress() {
        const address = addressInput.value.trim();
        
        if (address === '') {
            showError(addressInput, addressError, 'Address is required');
            return false;
        } else if (address.length < 5) {
            showError(addressInput, addressError, 'Please enter a complete address');
            return false;
        } else {
            clearError(addressInput, addressError);
            return true;
        }
    }
    
    // Validate password
    function validatePassword() {
        const password = passwordInput.value.trim();
        
        if (password === '') {
            showError(passwordInput, passwordError, 'Password is required');
            return false;
        } else if (password.length < 6) {
            showError(passwordInput, passwordError, 'Password must be at least 6 characters');
            return false;
        } else {
            clearError(passwordInput, passwordError);
            return true;
        }
    }
    
    // Validate password confirmation
    function validateConfirmPassword() {
        const password = passwordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();
        
        if (confirmPassword === '') {
            showError(confirmPasswordInput, confirmPasswordError, 'Please confirm your password');
            return false;
        } else if (password !== confirmPassword) {
            showError(confirmPasswordInput, confirmPasswordError, 'Passwords do not match');
            return false;
        } else {
            clearError(confirmPasswordInput, confirmPasswordError);
            return true;
        }
    }
    
    // Handle file selection
    profileImageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            fileNameDisplay.textContent = this.files[0].name;
        } else {
            fileNameDisplay.textContent = 'No file selected';
        }
    });
    
    // Add input event listeners
    firstnameInput.addEventListener('input', () => {
        if (firstnameInput.value.trim() !== '') {
            validateFirstname();
        } else {
            clearError(firstnameInput, firstnameError);
        }
    });
    
    lastnameInput.addEventListener('input', () => {
        if (lastnameInput.value.trim() !== '') {
            validateLastname();
        } else {
            clearError(lastnameInput, lastnameError);
        }
    });
    
    ageInput.addEventListener('input', () => {
        if (ageInput.value.trim() !== '') {
            validateAge();
        } else {
            clearError(ageInput, ageError);
        }
    });
    
    emailInput.addEventListener('input', () => {
        if (emailInput.value.trim() !== '') {
            validateEmail();
        } else {
            clearError(emailInput, emailError);
        }
    });
    
    phoneInput.addEventListener('input', () => {
        if (phoneInput.value.trim() !== '') {
            validatePhone();
        } else {
            clearError(phoneInput, phoneError);
        }
    });
    
    addressInput.addEventListener('input', () => {
        if (addressInput.value.trim() !== '') {
            validateAddress();
        } else {
            clearError(addressInput, addressError);
        }
    });
    
    passwordInput.addEventListener('input', () => {
        if (passwordInput.value.trim() !== '') {
            validatePassword();
            if (confirmPasswordInput.value.trim() !== '') {
                validateConfirmPassword();
            }
        } else {
            clearError(passwordInput, passwordError);
        }
    });
    
    confirmPasswordInput.addEventListener('input', () => {
        if (confirmPasswordInput.value.trim() !== '') {
            validateConfirmPassword();
        } else {
            clearError(confirmPasswordInput, confirmPasswordError);
        }
    });
    
    // Add blur event listeners
    firstnameInput.addEventListener('blur', validateFirstname);
    lastnameInput.addEventListener('blur', validateLastname);
    ageInput.addEventListener('blur', validateAge);
    emailInput.addEventListener('blur', validateEmail);
    phoneInput.addEventListener('blur', validatePhone);
    addressInput.addEventListener('blur', validateAddress);
    passwordInput.addEventListener('blur', validatePassword);
    confirmPasswordInput.addEventListener('blur', validateConfirmPassword);
    
    // Form submission
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        let isValid = true;
        
        // Validate all fields
        if (!validateFirstname()) isValid = false;
        if (!validateLastname()) isValid = false;
        if (!validateAge()) isValid = false;
        if (!validateEmail()) isValid = false;
        if (!validatePhone()) isValid = false;
        if (!validateAddress()) isValid = false;
        if (!validatePassword()) isValid = false;
        if (!validateConfirmPassword()) isValid = false;
        
        if (isValid) {
            // If validation passes, submit the form
            this.submit();
        }
    });
}); 