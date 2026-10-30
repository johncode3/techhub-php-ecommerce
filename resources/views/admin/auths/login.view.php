<?php
$errors = $_SESSION['login_errors'] ?? [];
$accessError = $_GET['error'] ?? null;
$oldInput = $_SESSION['login_old_input'] ?? [];
$successMessage = $_SESSION['login_success'] ?? null;
unset($_SESSION['login_errors'], $_SESSION['login_old_input'], $_SESSION['login_success']);
?>

<main class="login">
        <form class="auth-form" method="POST" action="index.php?page=login-store" novalidate>
            <img src="assets/images/logo.avif" alt="logo" class="logo-form">
            <h2>Login</h2>

            <?php if ($successMessage): ?>
                <div class="auth-alert success">
                    <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
                
            <?php if ($accessError): ?>
                <div class="auth-alert">
                    <ul class="auth-alert-error">
                        <li><?= htmlspecialchars($accessError, ENT_QUOTES, 'UTF-8') ?></li>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if(!empty($errors)): ?>
                <div class="auth-alert">
                    <ul class="auth-alert-error">
                        <?php foreach($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <label for="login_identifier">Email or Username</label>
            <input
                type="text"
                id="login_identifier"
                name="login_identifier"
                placeholder="enter email or username"
                value="<?= htmlspecialchars($oldInput['login_identifier'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                required>

            <label for="password">Password</label>
            <div class="password-container">
                <input
                type="password"
                id="password"
                name="password"
                placeholder="enter password"
                required>
                <i class="fas fa-eye toggle-password"></i>
            </div>

            <h4 class="forget-pass">Forget password ?</h4>
            <h4 class="login-with">Login with</h4>
            
            <div class="social-icon">
                <i class="fab fa-facebook-f"></i> 
                <i class="fab fa-instagram"></i>
                <i class="fab fa-google"></i>
                <i class="fab fa-twitter"></i> </div>
            
            <button type="submit">Login</button>

            <h3 class="signup">Don't have account? <a href="index.php?page=register"> Sign Up</a></h3>
        </form>
    </main>

<script src="assets/js/pages/auth.js"></script>
