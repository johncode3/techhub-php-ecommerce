<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechHub Admin</title>
    <link rel="stylesheet" href="assets/css/pages/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php
/** @var string $page */
$page = $page ?? '';
?>
    <div class="admin-layout">
        <!-- LEFT SIDE : Sidebar -->
        <aside class="admin-sidebar">
            <h2>TechHub Admin</h2>
            <nav>
                <a href="index.php?page=admin-dashboard" class="<?= ($page === 'admin-dashboard') ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                
                <h4 class="sidebar-heading">Catalog</h4>
                <a href="index.php?page=admin-categories" class="<?= ($page === 'admin-categories') ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i> Categories
                </a>
                <a href="index.php?page=admin-subcategories" class="<?= ($page === 'admin-subcategories') ? 'active' : '' ?>">
                    <i class="fas fa-list-alt"></i> Sub Categories
                </a>
                <a href="index.php?page=admin-brands" class="<?= ($page === 'admin-brands') ? 'active' : '' ?>">
                    <i class="fas fa-cube"></i> Brands
                </a>
                <a href="index.php?page=admin-products" class="<?= ($page === 'admin-products') ? 'active' : '' ?>">
                    <i class="fas fa-box"></i> Products
                </a>

                <h4 class="sidebar-heading">Sales & Inventory</h4>
                <a href="index.php?page=admin-orders" class="<?= ($page === 'admin-orders') ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart"></i> Orders
                </a>
                <a href="index.php?page=admin-reports" class="<?= ($page === 'admin-reports') ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i> Sales Report
                </a>
                <a href="index.php?page=admin-inventory" class="<?= ($page === 'admin-inventory') ? 'active' : '' ?>">
                    <i class="fas fa-warehouse"></i> Inventory
                </a>

                <h4 class="sidebar-heading">Settings</h4>
                <a href="index.php?page=admin-users" class="<?= ($page === 'admin-users') ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Users
                </a>
                <a href="index.php?page=admin-my-account" class="<?= ($page === 'admin-my-account') ? 'active' : '' ?>">
                    <i class="fas fa-user-circle"></i> My Account
                </a>

                <hr>
                <a href="index.php?page=home" style="color: #ffb8b8;">
                    <i class="fas fa-sign-out-alt"></i> Back to Store
                </a>
            </nav>
        </aside>

        <!-- RIGHT SIDE : Main Content -->
        <div class="admin-content">
            <header class="admin-topbar">
                <?php
                // 1. Unified Flash Message System
                $flash = null;
                if (!empty($_SESSION['flash'])) {
                    $flash = $_SESSION['flash'];
                    unset($_SESSION['flash']);
                } 
                // Fallback for old GET parameters (Optional)
                else if (isset($_GET['success'])) {
                    $flash = ['type' => 'success', 'message' => 'Action completed successfully!'];
                }

                if ($flash) :
                ?>
                    <div class="flash <?= ($flash['type'] === 'success') ? 'flash-success' : 'flash-error'; ?>" id="topbar-flash">
                        <div class="flash-message">
                            <i class="fas <?= ($flash['type'] === 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                            <?= htmlspecialchars($flash['message']); ?>
                        </div>
                        <button class="flash-close" onclick="document.getElementById('topbar-flash').style.display='none'">&times;</button>
                    </div>
                    <script>
                        setTimeout(function(){
                            var el = document.getElementById('topbar-flash');
                            if (el) {
                                el.style.opacity = '0';
                                setTimeout(() => el.style.display = 'none', 500);
                            }
                        }, 4000);
                    </script>
                <?php endif; ?>

                <div class="admin-info">
                    <div class="user-text">
                        <span class="admin-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'John'); ?></span>
                        <span class="role-badge"><?php echo htmlspecialchars($_SESSION['role'] ?? 'admin'); ?></span>
                    </div>
                    <i class="fas fa-user-circle fa-2x"></i>
                </div>
            </header>
            <!-- Don't close this div cuz it wraps around dashboard.view.php -->