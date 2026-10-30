<?php
/** @var string $page */

// 1. DATA FOR PRODUCT DETAIL PAGE
if ($page === 'product_detail') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $product = null;
    $related = [];

    if ($id > 0 && isset($pdo)) {
        try {
            // THE JOIN: We need the Brand and Category Names for the UI
            $stmt = $pdo->prepare("SELECT p.*, b.name as brand_name, c.name as category_name, sc.name as sub_category_name 
                                   FROM products p 
                                   LEFT JOIN brands b ON p.brand_id = b.id 
                                   LEFT JOIN categories c ON p.category_id = c.id 
                                   LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id
                                   WHERE p.id = ? AND p.status = 1");
            $stmt->execute([$id]);
            $product = $stmt->fetch();

            if ($product) {
                // Decode Thumbnails
                if (!empty($product['thumbnails'])) {
                    $decoded = json_decode($product['thumbnails'], true);
                    $product['thumbnails'] = is_array($decoded) ? $decoded : [];
                }

                // Fetch Related Products (same category, excluding current product)
                // We use JOIN here too so the related cards show the category name
                $relStmt = $pdo->prepare("SELECT p.*, c.name as category_name, sc.name as sub_category_name
                                         FROM products p 
                                         LEFT JOIN categories c ON p.category_id = c.id
                                         LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id
                                         WHERE p.category_id = ? AND p.id != ? AND p.status = 1 
                                         LIMIT 8");
                $relStmt->execute([$product['category_id'], $id]);
                $related = $relStmt->fetchAll();
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }
}

// 2. DATA FOR HOME PAGE (Looking perfect, John!)
if ($page === 'home') {
    $stmt = $pdo->query("SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 1
          AND p.stock > 0
          AND (p.is_new = 1 OR (p.original_price > 0 AND p.original_price > p.price))
        ORDER BY p.created_at DESC
        LIMIT 12");
    $featured_products = $stmt->fetchAll();
}

// 3. DATA FOR SHOP PAGE (index.php?page=product)
if ($page === 'product') {
    // A. Fetch Categories + Count + A-Z (THE ONLY CATEGORY QUERY YOU NEED)
    $shop_categories = $pdo->query("
        SELECT c.*, COUNT(p.id) as product_count 
        FROM categories c 
        LEFT JOIN products p ON c.id = p.category_id AND p.status = 1
        WHERE c.status = 1 
        GROUP BY c.id 
        ORDER BY c.name ASC
    ")->fetchAll();

    $shop_sub_categories = $pdo->query("
        SELECT sc.*, c.name AS parent_name, COUNT(p.id) as product_count 
        FROM sub_categories sc 
        JOIN categories c ON sc.category_id = c.id
        LEFT JOIN products p ON sc.id = p.sub_category_id AND p.status = 1
        WHERE sc.status = 1 
        GROUP BY sc.id 
        ORDER BY sc.name ASC
    ")->fetchAll();

    // B. Fetch Brands + Count + A-Z (THE ONLY BRAND QUERY YOU NEED)
    $shop_brands = $pdo->query("
        SELECT b.*, COUNT(p.id) as product_count 
        FROM brands b 
        LEFT JOIN products p ON b.id = p.brand_id AND p.status = 1
        WHERE b.status = 1 
        GROUP BY b.id 
        ORDER BY b.name ASC
    ")->fetchAll();

    // C. Logic for filtering products in the main grid
    $where = " WHERE p.status = 1";
    $params = [];

    // 1. Filter by Category if ID is in URL
    if (!empty($_GET['category_id'])) {
        $where .= " AND p.category_id = ?";
        $params[] = (int)$_GET['category_id'];
    }

    // 2. Filter by Brand if ID is in URL
    if (!empty($_GET['brand_id'])) {
        $where .= " AND p.brand_id = ?";
        $params[] = (int)$_GET['brand_id'];
    }

    // 3. Filter by Search if text is in URL
    if (!empty($_GET['search'])) {
        $where .= " AND p.name LIKE ?";
        $params[] = "%" . $_GET['search'] . "%";
    }

    // 4. Filter by Sub Category if ID is in URL
    if (!empty($_GET['sub_category_id'])) {
        $where .= " AND p.sub_category_id = ?";
        $params[] = (int)$_GET['sub_category_id'];
    }
    
    // 4. Sorting (whitelisted)
    $selected_sort = isset($_GET['sort']) ? (string)$_GET['sort'] : 'featured';
    $sort_map = [
        'featured' => 'p.created_at DESC',
        'price-low' => 'p.price ASC',
        'price-high' => 'p.price DESC',
        'newest' => 'p.created_at DESC',
        'best-selling' => 'p.created_at DESC',
        'rating' => 'p.created_at DESC',
    ];
    $order_by = $sort_map[$selected_sort] ?? $sort_map['featured'];

    // --- NEW: PAGINATION LOGIC ---
    $countSql = "SELECT COUNT(p.id) FROM products p $where";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total_products = $countStmt->fetchColumn();

    $limit = 15; // Products per page
    $total_pages = ceil($total_products / $limit);
    $current_page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
    $offset = ($current_page - 1) * $limit;
    // -----------------------------

    // D. Final SQL for the products
    $sql = "SELECT p.*, c.name AS category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            $where 
            ORDER BY $order_by 
            LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $all_products = $stmt->fetchAll();
}