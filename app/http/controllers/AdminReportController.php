<?php
protectAdmin();

$errors = [];
$reportRows = [];
$summary = [
    'order_count' => 0,
    'total_revenue' => 0,
];

$statsStmt = $pdo->query("\n    SELECT\n        COUNT(*) AS total_products,\n        COALESCE(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END), 0) AS active_products,\n        COALESCE(SUM(CASE WHEN stock <= 5 THEN 1 ELSE 0 END), 0) AS low_stock_products,\n        COALESCE(ROUND(SUM(price * stock), 2), 0) AS inventory_value\n    FROM products\n");
$statsRow = $statsStmt->fetch() ?: [];

$stats = [
    'total_products' => (int)($statsRow['total_products'] ?? 0),
];


// 1. Get Date Range from URL
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

if ($startDate === '' || $endDate === '') {
    $endDate = date('Y-m-d');
    $startDate = date('Y-m-d', strtotime('-6 days'));
}

try {
    // 2. Fetch Orders for the specific date range (grouped per day)
    $stmt = $pdo->prepare("SELECT DATE(created_at) AS order_date,
        COUNT(*) AS order_count,
        SUM(total_amount) AS total_revenue
        FROM orders
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at) DESC");
    $stmt->execute([$startDate, $endDate]);
    $reportRows = $stmt->fetchAll();

    foreach ($reportRows as $row) {
        $summary['order_count'] += (int)$row['order_count'];
        $summary['total_revenue'] += (float)$row['total_revenue'];
    }
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}