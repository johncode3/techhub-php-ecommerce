<?php
// Calculate total items in cart
$cartCount = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        // We sum the quantities of every item in the bag
        $cartCount += $item['qty'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub | Premium Tech Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <?php if ($page === 'product_detail'): ?>
        <link rel="stylesheet" href="assets/css/pages/product-detail.css">
    <?php endif; ?>
</head>
<body>

    <header class="main-header">
        <div class="container">
            <a href="index.php?page=home"><img src="assets/images/logo.avif" class="logo" alt="TechHub Logo"></a>
            <nav class="main-nav">
                <a href="index.php?page=home" class="<?= ($page == 'home') ? 'active' : '' ?>"><i class="fas fa-home"></i>Home</a>
                <a href="index.php?page=product" class="<?= ($page == 'product') ? 'active' : '' ?>"><i class="fas fa-shopping-bag"></i>Products</a>
                <a href="index.php?page=cart" class="cart-link <?= ($page == 'cart') ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i>Cart<span class="cart-count"><?= $cartCount ?></span></a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- If logged in: Show Dashboard & Logout -->
                    <?php
                    $accountTarget = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin')
                        ? 'admin-dashboard'
                        : 'account';
                    $accountActive = ($page === $accountTarget);
                    ?>
                    <a href="index.php?page=<?= $accountTarget ?>" class="<?= $accountActive ? 'active' : '' ?>">
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <i class="fas fa-gauge"></i> Admin Dashboard
                        <?php else: ?>
                            <i class="fas fa-user"></i> Hello, <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                    </a>
                    <a href="index.php?page=logout" onclick="return confirm('Are you sure you want to log out?');"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <!-- If NOT logged in: Show Login -->
                    <a href="index.php?page=login" class="<?= ($page == 'login' || $page == 'register') ? 'active' : '' ?>">
                        <i class="fas fa-sign-in-alt"></i> Login / Register
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>