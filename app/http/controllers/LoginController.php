<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?page=login");
    exit;
}

// 1. Grab inputs safely
$login_identifier = trim($_POST['login_identifier'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

// 2. Basic Validation Rules
if ($login_identifier === '') {
    $errors[] = 'Please enter your email or username.';
}

if ($password === '') {
    $errors[] = 'Please enter your password.';
}

$oldInput = ['login_identifier' => $login_identifier];

if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    $_SESSION['login_old_input'] = $oldInput;
    header("Location: index.php?page=login");
    exit;
}

// 3. Database Check (Look for the user by Email OR Username)
try {
    $stmt = $pdo->prepare('SELECT id, username, email, password, role FROM users WHERE email = :email OR username = :username LIMIT 1');
    $stmt->execute([
        ':email' => $login_identifier,
        ':username' => $login_identifier
    ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        if ($user['role'] === 'admin') {
            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php?page=admin-dashboard';
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
        } else {
            header("Location: index.php?page=account");
        }
        exit;
    } else {
        $_SESSION['login_errors'] = ['Invalid email/username or password.'];
        $_SESSION['login_old_input'] = $oldInput;
        header("Location: index.php?page=login");
        exit;
    }

} catch (PDOException $exception) {
    $_SESSION['login_errors'] = ['System error. Please try again later.'];
    $_SESSION['login_old_input'] = $oldInput;
    header("Location: index.php?page=login");
    exit;
}