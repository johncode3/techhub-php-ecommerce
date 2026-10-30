<?php
protectAdmin();

$statsStmt = $pdo->query("\n    SELECT\n        COUNT(*) AS total_products,\n        COALESCE(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END), 0) AS active_products,\n        COALESCE(SUM(CASE WHEN stock <= 5 THEN 1 ELSE 0 END), 0) AS low_stock_products,\n        COALESCE(ROUND(SUM(price * stock), 2), 0) AS inventory_value\n    FROM products\n");
$statsRow = $statsStmt->fetch() ?: [];

$usersStmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$usersRow = $usersStmt->fetch() ?: [];

$stats = [
    'total_products' => (int)($statsRow['total_products'] ?? 0),
    'active_products' => (int)($statsRow['active_products'] ?? 0),
    'low_stock_products' => (int)($statsRow['low_stock_products'] ?? 0),
    'active_users' => (int)($usersRow['total_users'] ?? 0),
    'inventory_value' => number_format((float)($statsRow['inventory_value'] ?? 0), 2),
];

$productBaseSql = "\n    SELECT p.id, p.name, p.main_image, p.price, p.stock, p.status, p.created_at,\n           c.name AS category_name,\n           sc.name AS sub_category_name,\n           b.name AS brand_name\n    FROM products p\n    LEFT JOIN categories c ON p.category_id = c.id\n    LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id\n    LEFT JOIN brands b ON p.brand_id = b.id\n";

$recentProductsStmt = $pdo->query($productBaseSql . " ORDER BY p.id DESC LIMIT 5");
$recentProducts = $recentProductsStmt->fetchAll();

$lowStockProductsStmt = $pdo->query($productBaseSql . " WHERE p.stock <= 5 ORDER BY p.stock ASC, p.id DESC LIMIT 8");
$lowStockProducts = $lowStockProductsStmt->fetchAll();
