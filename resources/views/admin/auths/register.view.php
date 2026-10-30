<?php
$errors = $_SESSION['register_errors'] ?? [];
$oldInput = $_SESSION['register_old_input'] ?? [];
$successMessage = $_SESSION['register_success'] ?? null;
unset($_SESSION['register_errors'], $_SESSION['register_old_input'], $_SESSION['register_success']);
?>

<main class="login">
        <form class="auth-form" method="POST" action="index.php?page=register-store" novalidate>
            <img src="assets/images/logo.avif" alt="logo" class="logo-form">
            <h2>Sign Up</h2>

            <?php if ($successMessage): ?>
                <div class="auth-alert success">
                    <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="auth-alert">
                    <ul class="auth-alert-error">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="enter email" 
                        value="<?= htmlspecialchars($oldInput['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    />

            <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="enter username"
                        value="<?= htmlspecialchars($oldInput['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >

            <label for="password">Password</label>
                    <div class="password-container">
                        <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="enter password"
                        required
                        />
                        <i class="fas fa-eye toggle-password"></i>
                    </div>

            <label for="confirm-password">Confirm Password</label>
                    <div class="password-container">
                        <input
                            type="password"
                            id="confirm-password"
                            name="confirm-password"
                            placeholder="confirm password"
                            required
                        >
                        <i class="fas fa-eye toggle-password"></i>
                    </div>

            <h4 class="login-with">Sign Up with</h4>
            
            <div class="social-icon">
                <i class="fab fa-facebook-f"></i> 
                <i class="fab fa-instagram"></i>
                <i class="fab fa-google"></i>
                <i class="fab fa-twitter"></i> </div>
            
            <button type="submit">Sign Up</button>

            <h3 class="signup">Already have account? <a href="index.php?page=login"> Login</a></h3>
        </form>
    </main>
<script src="assets/js/pages/auth.js"></script>