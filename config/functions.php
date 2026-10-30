<?php
function isAdmin() {
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}

function protectAdmin() {
    if (!isAdmin()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'index.php?page=admin-dashboard';
        header('Location: index.php?page=login&error=You must be an admin to access this page');
        exit;
    }
}

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function isLogin(){
    return isset($_SESSION['user_id']);
}

function loginRequire(){
    if(!isLogin()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'index.php?page=checkout';
        header('Location: index.php?page=login&error=Please login to continue');
        exit;
    }
}