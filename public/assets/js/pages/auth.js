// Toggle Password Visibility (Handles multiple password fields!)
const togglePasswords = document.querySelectorAll('.toggle-password');

togglePasswords.forEach(function (toggleIcon) {
    toggleIcon.addEventListener('click', function () {
        const passwordInput = this.parentElement.querySelector('input');

        if (passwordInput) {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        }
    });
});  