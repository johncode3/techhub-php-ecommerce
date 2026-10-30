<?php
protectAdmin();

$errors = [];
$adminUser = null;

try {
    $userId = (int)($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) {
        $errors[] = 'Invalid user session.';
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
                $errors[] = 'All password fields are required.';
            } elseif ($newPassword !== $confirmPassword) {
                $errors[] = 'New password and confirmation do not match.';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'New password must be at least 6 characters.';
            } else {
                $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
                $stmt->execute([$userId]);
                $row = $stmt->fetch();

                if (!$row || !password_verify($currentPassword, $row['password'])) {
                    $errors[] = 'Current password is incorrect.';
                } else {
                    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                    $update = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
                    $update->execute([$hashed, $userId]);
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Password updated successfully.'];
                    header('Location: index.php?page=admin-my-account');
                    exit;
                }
            }
        }

        $userStmt = $pdo->prepare('SELECT id, username, email, role, created_at FROM users WHERE id = ? LIMIT 1');
        $userStmt->execute([$userId]);
        $adminUser = $userStmt->fetch();
    }
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}
