<?php
protectAdmin();

$lowStockThreshold = 5;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inventory_adjust'])) {
    $productId = (int)($_POST['product_id'] ?? 0);
    $action = $_POST['adjust_action'] ?? 'set';
    $qty = (int)($_POST['adjust_qty'] ?? 0);
    $redirect = $_POST['redirect'] ?? 'index.php?page=admin-inventory';

    if ($productId > 0 && $qty > 0) {
        if ($action === 'set') {
            $newStock = max(0, $qty);
            $stmt = $pdo->prepare('UPDATE products SET stock = ? WHERE id = ?');
            $stmt->execute([$newStock, $productId]);
        } else {
            $delta = $action === 'sub' ? -$qty : $qty;
            $stmt = $pdo->prepare('UPDATE products SET stock = GREATEST(stock + ?, 0) WHERE id = ?');
            $stmt->execute([$delta, $productId]);
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Stock updated.'];
    }

    header('Location: ' . $redirect);
    exit;
}

$inventoryProducts = [];
$inventoryCategories = [];
$inventoryBrands = [];
$inventoryStats = [
    'total_skus' => 0,
    'low_stock' => 0,
    'out_of_stock' => 0,
    'inventory_value' => 0,
];

// Filters
$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$categoryId = $_GET['category_id'] ?? '';
$brandId = $_GET['brand_id'] ?? '';
$stockLevel = $_GET['stock_level'] ?? '';

$where = ' WHERE 1=1';
$params = [];

if ($search !== '') {
    $where .= ' AND p.name LIKE ?';
    $params[] = '%' . $search . '%';
}

if ($status !== '' && ($status === '0' || $status === '1')) {
    $where .= ' AND p.status = ?';
    $params[] = (int)$status;
}

if ($categoryId !== '') {
    $where .= ' AND p.category_id = ?';
    $params[] = (int)$categoryId;
}

if ($brandId !== '') {
    $where .= ' AND p.brand_id = ?';
    $params[] = (int)$brandId;
}

if ($stockLevel === 'out') {
    $where .= ' AND p.stock <= 0';
} elseif ($stockLevel === 'low') {
    $where .= ' AND p.stock > 0 AND p.stock <= ?';
    $params[] = $lowStockThreshold;
} elseif ($stockLevel === 'in') {
    $where .= ' AND p.stock > ?';
    $params[] = $lowStockThreshold;
}

// Main inventory table
$sql = "SELECT p.id, p.name, p.main_image, p.price, p.stock, p.status,
               c.name AS category_name,
               b.name AS brand_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        $where
        ORDER BY p.stock ASC, p.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inventoryProducts = $stmt->fetchAll();

// Summary stats (overall, not filtered)
$statsSql = "SELECT
        COUNT(*) AS total_skus,
        SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) AS out_of_stock,
        SUM(CASE WHEN stock > 0 AND stock <= ? THEN 1 ELSE 0 END) AS low_stock,
        COALESCE(ROUND(SUM(price * stock), 2), 0) AS inventory_value
    FROM products";

$statsStmt = $pdo->prepare($statsSql);
$statsStmt->execute([$lowStockThreshold]);
$statsRow = $statsStmt->fetch() ?: [];

$inventoryStats = [
    'total_skus' => (int)($statsRow['total_skus'] ?? 0),
    'low_stock' => (int)($statsRow['low_stock'] ?? 0),
    'out_of_stock' => (int)($statsRow['out_of_stock'] ?? 0),
    'inventory_value' => number_format((float)($statsRow['inventory_value'] ?? 0), 2),
];

// Dropdown data
$inventoryCategories = $pdo->query("SELECT id, name FROM categories WHERE status = 1 ORDER BY name ASC")->fetchAll();
$inventoryBrands = $pdo->query("SELECT id, name FROM brands WHERE status = 1 ORDER BY name ASC")->fetchAll();
