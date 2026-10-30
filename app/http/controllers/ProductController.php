<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../database/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;
$related = [];

if ($id > 0 && isset($pdo) && $pdo instanceof PDO) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();
        if ($product) {
            if (!empty($product['thumbnails'])) {
                $decoded = json_decode($product['thumbnails'], true);
                $product['thumbnails'] = is_array($decoded) ? $decoded : [];
            } else {
                $product['thumbnails'] = [];
            }

            $relStmt = $pdo->prepare('SELECT id, name, price, original_price, stock, rating, review_count, main_image, thumbnails, category FROM products WHERE category = :cat AND id != :id LIMIT 4');
            $relStmt->execute([':cat' => $product['category'], ':id' => $id]);
            $related = $relStmt->fetchAll();
            foreach ($related as &$r) {
                if (!empty($r['thumbnails'])) {
                    $decoded = json_decode($r['thumbnails'], true);
                    $r['thumbnails'] = is_array($decoded) ? $decoded : [];
                } else {
                    $r['thumbnails'] = [];
                }
            }
            unset($r);
        }
    } catch (PDOException $e) {
        $product = null;
        $related = [];
    }
}

require_once '../resources/views/pages/product_detail.view.php';

exit;
