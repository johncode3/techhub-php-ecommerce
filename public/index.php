<?php
require_once dirname(__DIR__) . '/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if ($page === 'cart-add') {
    require_once '../app/http/controllers/CartController.php';
    exit;
}

if ($page === 'place-order') {
    require_once '../app/http/controllers/OrderController.php';
    exit;
}

if ($page === 'login-store') {
    require_once '../app/http/controllers/LoginController.php';
    exit;
}

if ($page === 'logout') {
    require_once '../app/http/controllers/LogoutController.php';
    exit;
}

if ($page === 'register-store') {
    require_once '../app/http/controllers/RegisterController.php';
    exit;
}

if ($page === 'cart-remove') {
    $key = $_GET['key'] ?? '';
    
    if (isset($_SESSION['cart'][$key])) {
        unset($_SESSION['cart'][$key]);
        
        $_SESSION['flash'] = [
            'type' => 'success', 
            'message' => 'Item removed from your cart.'
        ];
    }

    header("Location: index.php?page=cart");
    exit;
}

if ($page === 'cart-update') {
    $key = $_GET['key'] ?? '';
    $action = $_GET['action'] ?? '';

    if (isset($_SESSION['cart'][$key])) {
        if ($action === 'plus') {
            $_SESSION['cart'][$key]['qty']++;
        } elseif ($action === 'minus' && $_SESSION['cart'][$key]['qty'] > 1) {
            $_SESSION['cart'][$key]['qty']--;
        }
    }
    header("Location: index.php?page=cart");
    exit;
}

// Route to Admin or Customer
if (strpos($page, 'admin-') === 0) {
    // Admin
    switch ($page) {
        // Admin Dashboard
        case 'admin-dashboard':
            require_once '../app/http/controllers/AdminDashboardController.php';
            include '../resources/views/admin/dashboard.view.php';
            break;

        // Category Management
        case 'admin-categories':
            require_once '../app/http/controllers/AdminCategoryController.php';
            include '../resources/views/admin/categories/index.view.php';
            break;
        case 'admin-category-create':
            require_once '../app/http/controllers/AdminCategoryController.php';
            include '../resources/views/admin/categories/create.view.php';
            break;
        case 'admin-category-edit':
            require_once '../app/http/controllers/AdminCategoryController.php';
            include '../resources/views/admin/categories/edit.view.php';
            break;
        case 'admin-category-delete':
            require_once '../app/http/controllers/AdminCategoryController.php';
        case 'admin-category-show':
            require_once '../app/http/controllers/AdminCategoryController.php';
            include '../resources/views/admin/categories/show.view.php';
            break;
        
        // Subcategory Management
        case 'admin-subcategories':
            require_once '../app/http/controllers/AdminSubCategoryController.php';
            include '../resources/views/admin/sub_categories/index.view.php';
            break;
        case 'admin-subcategory-create':
            require_once '../app/http/controllers/AdminSubCategoryController.php';
            include '../resources/views/admin/sub_categories/create.view.php';
            break;
        case 'admin-subcategory-edit':
            require_once '../app/http/controllers/AdminSubCategoryController.php';
            include '../resources/views/admin/sub_categories/edit.view.php';
            break;
        case 'admin-subcategory-delete':
            require_once '../app/http/controllers/AdminSubCategoryController.php';
        case 'admin-subcategory-show':
            require_once '../app/http/controllers/AdminSubCategoryController.php';
            include '../resources/views/admin/sub_categories/show.view.php';
            break;
        // Brand Management
        case 'admin-brands':
            require_once '../app/http/controllers/AdminBrandController.php';
            include '../resources/views/admin/brands/index.view.php';
            break;
        case 'admin-brand-create':
            require_once '../app/http/controllers/AdminBrandController.php';
            include '../resources/views/admin/brands/create.view.php';
            break;
        case 'admin-brand-edit':
            require_once '../app/http/controllers/AdminBrandController.php';
            include '../resources/views/admin/brands/edit.view.php';
            break;
        case 'admin-brand-delete':
            require_once '../app/http/controllers/AdminBrandController.php';
        case 'admin-brand-show':
            require_once '../app/http/controllers/AdminBrandController.php';
            include '../resources/views/admin/brands/show.view.php';
            break;
        // Product Management
        case 'admin-products':
            require_once '../app/http/controllers/AdminProductController.php';
            include '../resources/views/admin/products/index.view.php';
            break;
        case 'admin-product-create':
            require_once '../app/http/controllers/AdminProductController.php';
            include '../resources/views/admin/products/create.view.php';
            break;
        case 'admin-product-edit':
            require_once '../app/http/controllers/AdminProductController.php';
            include '../resources/views/admin/products/edit.view.php';
            break;
        case 'admin-product-show':
            require_once '../app/http/controllers/AdminProductController.php';
            include '../resources/views/admin/products/show.view.php';
            break;
        case 'admin-product-delete':
            require_once '../app/http/controllers/AdminProductController.php';
            break;
        // Order Management
        case 'admin-orders':
            require_once '../app/http/controllers/AdminOrderController.php';
            include '../resources/views/admin/orders/index.view.php';
            break;
        case 'admin-order-show':
            require_once '../app/http/controllers/AdminOrderController.php';
            include '../resources/views/admin/orders/show.view.php';
            break;
        // Report Management
        case 'admin-reports':
            require_once '../app/http/controllers/AdminReportController.php';
            include '../resources/views/admin/reports/index.view.php';
            break;

        // Inventory Management
        case 'admin-inventory':
            require_once '../app/http/controllers/AdminInventoryController.php';
            include '../resources/views/admin/inventory/index.view.php';
            break;
            
        // Users
        case 'admin-users':
            require_once '../app/http/controllers/AdminUserController.php';
            include '../resources/views/admin/users/index.view.php';
            break;
        case 'admin-my-account':
            require_once '../app/http/controllers/AdminMyAccountController.php';
            include '../resources/views/admin/my_account.view.php';
            break;
        default:
            echo "<div style='padding:50px; text-align:center;'><h2>404 - Admin Section Not Found!!!</h2><a href='index.php?page=admin-dashboard'>Back to Dashboard</a></div>";
            break;
    }
    
    exit;
}

// Customer
// Load Header
require_once '../resources/views/layouts/header.view.php';

// Public Route
switch ($page) {
    case 'home':
        require_once '../app/http/controllers/PublicProductController.php';
        include '../resources/views/pages/home.view.php';
        break;

    case 'product':
        require_once '../app/http/controllers/PublicProductController.php';
        include '../resources/views/pages/products.view.php';
        break;

    case 'product_detail':
        require_once '../app/http/controllers/PublicProductController.php';
        include '../resources/views/pages/product_detail.view.php';
        break;

    case 'cart':
        include '../resources/views/pages/cart.view.php';
        break;
    
    case 'checkout':
        include '../resources/views/pages/checkout.view.php';
        break;

    case 'about':
        include '../resources/views/pages/about.view.php';
        break;

    case 'contact':
        include '../resources/views/pages/contact.view.php';
        break;

    case 'account':
        require_once '../app/http/controllers/AccountController.php';
        include '../resources/views/pages/account.view.php';
        break;

    case 'login':
        include '../resources/views/admin/auths/login.view.php';
        break;
    case 'register':
        include '../resources/views/admin/auths/register.view.php';
        break;

    default:
        echo "<div class='container' style='padding: 100px 0; text-align: center;'><h2>404 - Page Not Found!!!</h2><a href='index.php'>Return Home</a></div>";
        break;
}

// Skip footer for Auth
$auth_pages = ['login', 'login-store', 'register', 'register-store'];

if (!in_array($page, $auth_pages)) {
    require_once '../resources/views/layouts/footer.view.php';
} else {
    echo '<script src="assets/js/main.js"></script>';
    echo '</body></html>';
}