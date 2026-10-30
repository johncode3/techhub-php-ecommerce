<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?page=register");
    exit;
}

// 1. Grab inputs safely
$email = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm-password'] ?? '';

$errors = [];

// 2. Validation Rules
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}

if ($username === '') {
    $errors[] = 'Username is required.';
}

if (strlen($password) < 6) {
    $errors[] = 'Password must contain at least 6 characters.';
}

if ($password !== $confirmPassword) {
    $errors[] = 'Passwords do not match.';
}

$oldInput = [
    'email' => $email,
    'username' => $username,
];

if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_old_input'] = $oldInput;
    header("Location: index.php?page=register");
    exit;
}

// 3. Database Check (Is email or name already taken?)
try {
    $existingStmt = $pdo->prepare('SELECT email, username FROM users WHERE email = :email OR username = :username LIMIT 1');
    $existingStmt->execute([':email' => $email, ':username' => $username]);
    $existingUser = $existingStmt->fetch();

    if ($existingUser) {
        if (strcasecmp($existingUser['email'], $email) === 0) {
            $errors[] = 'This email is already registered.';
        }
        if (strcasecmp($existingUser['username'], $username) === 0) {
            $errors[] = 'This username is already taken.';
        }
    }
} catch (PDOException $exception) {
    $errors[] = 'Database error. Please try again.';
}

if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_old_input'] = $oldInput;
    header("Location: index.php?page=register");
    exit;
}

// 4. Save to Database
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $insertStmt = $pdo->prepare('INSERT INTO users (email, username, password, role) VALUES (:email, :username, :password, :role)');
    
    $insertStmt->execute([
        ':email' => $email,
        ':username' => $username,
        ':password' => $hashedPassword,
        ':role' => 'customer'
    ]);

    $_SESSION['login_success'] = 'Account created successfully. You can now sign in.';
    header("Location: index.php?page=login");
    exit;
    
} catch (PDOException $exception) {
    $_SESSION['register_errors'] = ['Unable to complete registration. Please try again shortly.'];
    $_SESSION['register_old_input'] = $oldInput;
    header("Location: index.php?page=register");
    exit;
}