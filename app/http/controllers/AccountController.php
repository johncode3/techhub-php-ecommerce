<?php
loginRequire();

$userId = (int)($_SESSION['user_id'] ?? 0);
$user = null;
$orders = [];
$selectedOrder = null;
$orderItems = [];
$errors = [];

try {
    if ($userId > 0) {
        $userStmt = $pdo->prepare('SELECT id, username, email, created_at FROM users WHERE id = ? LIMIT 1');
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch();

        $ordersStmt = $pdo->prepare('SELECT id, total_amount, order_status, payment_method, created_at
            FROM orders
            WHERE user_id = ?
            ORDER BY id DESC');
        $ordersStmt->execute([$userId]);
        $orders = $ordersStmt->fetchAll();

        $orderId = (int)($_GET["order_id"] ?? 0);
        if ($orderId > 0) {
            $orderStmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
            $orderStmt->execute([$orderId, $userId]);
            $selectedOrder = $orderStmt->fetch();

            if ($selectedOrder) {
                $itemsStmt = $pdo->prepare('SELECT product_name, price, qty, variant_info
                    FROM order_items
                    WHERE order_id = ?');
                $itemsStmt->execute([$orderId]);
                $orderItems = $itemsStmt->fetchAll();
            }
        }
    }
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}
