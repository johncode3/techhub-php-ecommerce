<?php
protectAdmin();

$page = $page ?? ($_GET['page'] ?? '');

$errors = [];
$orders = [];
$order = null;
$orderItems = [];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = trim($_POST['status'] ?? ($_POST['order_status'] ?? ''));
        $shouldUpdate = ($orderId > 0 && $status !== '' && (isset($_POST['update_status']) || isset($_POST['order_status'])));

        if ($shouldUpdate) {
            $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
            $stmt->execute([$status, $orderId]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => "Order #$orderId updated!"];

            $redirectPage = $page === 'admin-order-show'
                ? 'admin-order-show&id=' . $orderId
                : 'admin-orders';
            header("Location: index.php?page=$redirectPage");
            exit;
        }
    }

    if ($page === 'admin-orders') {
        $statusFilter = trim($_GET['status'] ?? '');
        $search = trim($_GET['search'] ?? '');

        $where = ' WHERE 1=1';
        $params = [];

        if ($statusFilter !== '') {
            $where .= ' AND o.order_status = ?';
            $params[] = $statusFilter;
        }

        if ($search !== '') {
            $where .= ' AND (o.full_name LIKE ? OR o.email LIKE ? OR o.id = ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = (int)$search;
        }

        $sql = "SELECT o.id, o.user_id, o.full_name, o.email, o.phone, o.total_amount, o.payment_method,
                       o.order_status, o.created_at, u.username AS user_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                $where
                ORDER BY o.id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll();
    }

    if ($page === 'admin-order-show') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $orderStmt = $pdo->prepare("SELECT o.*, u.username AS user_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ?");
            $orderStmt->execute([$id]);
            $order = $orderStmt->fetch();

            $itemsStmt = $pdo->prepare('SELECT oi.*, p.main_image
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?');
            $itemsStmt->execute([$id]);
            $orderItems = $itemsStmt->fetchAll();
        }
    }
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}
