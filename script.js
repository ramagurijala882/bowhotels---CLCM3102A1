document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contactForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const messageInput = document.getElementById('message');

    const nameError = document.getElementById('nameError');
    const emailError = document.getElementById('emailError');
    const messageError = document.getElementById('messageError');

    // Ensure HTTPS is used
    if (location.protocol === "http:") {
        location.replace("https://" + location.hostname + location.pathname);
    }

    // Form validation logic
    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default submission

        let isValid = true;

        // Name validation
        if (nameInput.value.trim() === '') {
            nameError.textContent = 'Name is required';
            isValid = false;
        } else if (nameInput.value.trim().length < 3) {
            nameError.textContent = 'Name must be at least 3 characters long';
            isValid = false;
        } else {
            nameError.textContent = '';
        }

        // Email validation
        if (emailInput.value.trim() === '') {
            emailError.textContent = 'Email is required';
            isValid = false;
        } else if (!validateEmail(emailInput.value)) {
            emailError.textContent = 'Invalid email format';
            isValid = false;
        } else {
            emailError.textContent = '';
        }

        // Message validation
        if (messageInput.value.trim() === '') {
            messageError.textContent = 'Message is required';
            isValid = false;
        } else if (messageInput.value.trim().length < 10) {
            messageError.textContent = 'Message must be at least 10 characters long';
            isValid = false;
        } else {
            messageError.textContent = '';
        }

        // If validation fails, stop submission
        if (!isValid) return;

        // Use SweetAlert2 for confirmation instead of confirm()
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to submit the form?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Submit",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Submit form if confirmed
            }
        });
    });

    // Email validation function
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }
});
