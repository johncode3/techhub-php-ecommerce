document.addEventListener('DOMContentLoaded', function() {
    // Listen for PLUS clicks
    document.querySelectorAll('.qty-btn.plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const key = this.parentElement.getAttribute('data-key');
            window.location.href = `index.php?page=cart-update&action=plus&key=${key}`;
        });
    });

    // Listen for MINUS clicks
    document.querySelectorAll('.qty-btn.minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const key = this.parentElement.getAttribute('data-key');
            window.location.href = `index.php?page=cart-update&action=minus&key=${key}`;
        });
    });
});