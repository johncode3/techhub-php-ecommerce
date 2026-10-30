<?php
protectAdmin();

$page = $page ?? ($_GET['page'] ?? '');

// Helper: normalize JSON thumbnails input
function normalizeThumbnailsInput($raw) {
    $raw = trim($raw ?? '');
    if ($raw === '') {
        return null;
    }
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return false;
    }
    return json_encode($decoded);
}

// Helper: safe slug from text
function getSafeSlug($text, $fallback) {
    $slug = strtolower(trim((string)$text));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug !== '' ? $slug : $fallback;
}

// Helper: category slug from category name
function getCategorySlug($category_id) {
    if (empty($category_id)) {
        return 'uncategorized';
    }
    global $pdo;
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->execute([(int)$category_id]);
    $name = (string)$stmt->fetchColumn();
    return getSafeSlug($name, 'uncategorized');
}

// Helper: subcategory slug from subcategory name
function getSubCategorySlug($sub_category_id) {
    if (empty($sub_category_id)) {
        return 'no-subcategory';
    }
    global $pdo;
    $stmt = $pdo->prepare("SELECT name FROM sub_categories WHERE id = ?");
    $stmt->execute([(int)$sub_category_id]);
    $name = (string)$stmt->fetchColumn();
    return getSafeSlug($name, 'no-subcategory');
}

// Helper: upload main image and return relative path
function handleMainImageUpload($file, $existingPath = null, $category_id = null, $sub_category_id = null, $product_name = null) {
    if (empty($file) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $existingPath;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return false;
    }
    $catSlug = getCategorySlug($category_id);
    $subSlug = getSubCategorySlug($sub_category_id);
    $productSlug = getSafeSlug($product_name, 'product');
    $baseDir = __DIR__ . '/../../../public/assets/images/product_images/' . $catSlug . '/' . $subSlug . '/' . $productSlug;
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0775, true);
    }
    $fileName = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetPath = $baseDir . '/' . $fileName;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return false;
    }

    if (!empty($existingPath)) {
        $oldPath = __DIR__ . '/../../../public/' . ltrim($existingPath, '/');
        if (is_file($oldPath)) {
            unlink($oldPath);
        }
    }

    return 'assets/images/product_images/' . $catSlug . '/' . $subSlug . '/' . $productSlug . '/' . $fileName;
}

// Helper: upload up to 3 thumbnail images and return JSON array
function handleThumbnailUploads($files, $existingJson = null, $category_id = null, $sub_category_id = null, $product_name = null) {
    if (empty($files) || !isset($files['error']) || !is_array($files['error'])) {
        return $existingJson;
    }

    $hasUpload = false;
    foreach ($files['error'] as $err) {
        if ($err !== UPLOAD_ERR_NO_FILE) {
            $hasUpload = true;
            break;
        }
    }
    if (!$hasUpload) {
        return $existingJson;
    }

    $existing = [];
    if (!empty($existingJson)) {
        $decoded = json_decode($existingJson, true);
        if (is_array($decoded)) {
            $existing = array_values(array_filter($decoded, 'is_string'));
        }
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $catSlug = getCategorySlug($category_id);
    $subSlug = getSubCategorySlug($sub_category_id);
    $productSlug = getSafeSlug($product_name, 'product');
    $baseDir = __DIR__ . '/../../../public/assets/images/product_images/' . $catSlug . '/' . $subSlug . '/' . $productSlug;
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0775, true);
    }
    $paths = [];

    $count = min(count($files['name']), 3);
    for ($i = 0; $i < $count; $i++) {
        if (count($paths) >= 3) {
            break;
        }
        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            return false;
        }
        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            return false;
        }
        $fileName = 'thumb_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $targetPath = $baseDir . '/' . $fileName;
        if (!move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
            return false;
        }
        $paths[] = 'assets/images/product_images/' . $catSlug . '/' . $subSlug . '/' . $productSlug . '/' . $fileName;
    }

    foreach ($existing as $old) {
        $oldPath = __DIR__ . '/../../../public/' . ltrim($old, '/');
        if (is_file($oldPath)) {
            unlink($oldPath);
        }
    }

    return json_encode($paths);
}

// --- 1. ACTION: ADD (Store) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $original_price = (float)($_POST['original_price'] ?? 0);
    $stock = (int)$_POST['stock'];
    $category_id = (int)$_POST['category_id'];
    $sub_category_id = (int)($_POST['sub_category_id'] ?? 0);
    $brand_id = (int)$_POST['brand_id'];
    $desc = trim($_POST['description']);
    $specs = trim($_POST['specifications']);
    $color = trim($_POST['color']);
    $storage = trim($_POST['storage']);
    $main_image = handleMainImageUpload($_FILES['main_image'] ?? null, null, $category_id, $sub_category_id, $name);
    if ($main_image === false) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Main image upload failed.'];
        header("Location: index.php?page=admin-product-create");
        exit();
    }
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $status = isset($_POST['status']) && $_POST['status'] !== '' ? (int)$_POST['status'] : 1;
    $rating = (float)($_POST['rating'] ?? 0);
    $review_count = (int)($_POST['review_count'] ?? 0);
    
    $thumbnails = handleThumbnailUploads($_FILES['thumbnails'] ?? null, null, $category_id, $sub_category_id, $name);
        if ($thumbnails === false) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Thumbnail upload failed.'];
            header("Location: index.php?page=admin-product-create");
            exit();
        }

    if (empty($name)) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Product name is required!'];
        header("Location: index.php?page=admin-product-create");
        exit();
    }

    // Check for duplicate product names
    $check = $pdo->prepare("SELECT id FROM products WHERE name = ?");
    $check->execute([$name]);

    if ($check->rowCount() > 0) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Product name already exists!'];
        header("Location: index.php?page=admin-product-create");
        exit();
    }

    $sql = "INSERT INTO products (category_id, sub_category_id, brand_id, name, price, original_price, stock, rating, review_count, status, description, specifications, color, storage, main_image, thumbnails, is_new) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $category_id,
        $sub_category_id > 0 ? $sub_category_id : null,
        $brand_id,
        $name,
        $price,
        $original_price,
        $stock,
        $rating,
        $review_count,
        $status,
        $desc !== '' ? $desc : null,
        $specs !== '' ? $specs : null,
        $color !== '' ? $color : null,
        $storage !== '' ? $storage : null,
        $main_image,
        $thumbnails,
        $is_new
    ]);
    
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Product created successfully!'];
    header("Location: index.php?page=admin-products");
    exit();
}

// --- 2. ACTION: EDIT (Update) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = (int)$_POST['product_id'];
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $original_price = (float)($_POST['original_price'] ?? 0);
    $stock = (int)$_POST['stock'];
    $category_id = (int)$_POST['category_id'];
    $sub_category_id = (int)($_POST['sub_category_id'] ?? 0);
    $brand_id = (int)$_POST['brand_id'];
    $desc = trim($_POST['description']);
    $specs = trim($_POST['specifications']);
    $color = trim($_POST['color']);
    $storage = trim($_POST['storage']);
    $status = (int)$_POST['status'];
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $rating = (float)($_POST['rating'] ?? 0);
    $review_count = (int)($_POST['review_count'] ?? 0);
    $main_image = handleMainImageUpload($_FILES['main_image'] ?? null, $_POST['existing_main_image'] ?? null, $category_id, $sub_category_id, $name);
    if ($main_image === false) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Main image upload failed.'];
        header("Location: index.php?page=admin-product-edit&id=$id");
        exit();
    }
    $thumbnails = handleThumbnailUploads($_FILES['thumbnails'] ?? null, $_POST['existing_thumbnails'] ?? null, $category_id, $sub_category_id, $name);
        if ($thumbnails === false) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Thumbnail upload failed.'];
            header("Location: index.php?page=admin-product-edit&id=$id");
            exit();
        }
    if ($id > 0 && !empty($name)) {
        $sql = "UPDATE products SET category_id = ?, sub_category_id = ?, brand_id = ?, name = ?, price = ?, original_price = ?, stock = ?, rating = ?, review_count = ?, status = ?, description = ?, specifications = ?, color = ?, storage = ?, main_image = ?, thumbnails = ?, is_new = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $category_id,
            $sub_category_id > 0 ? $sub_category_id : null,
            $brand_id,
            $name,
            $price,
            $original_price,
            $stock,
            $rating,
            $review_count,
            $status,
            $desc !== '' ? $desc : null,
            $specs !== '' ? $specs : null,
            $color !== '' ? $color : null,
            $storage !== '' ? $storage : null,
            $main_image,
            $thumbnails,
            $is_new,
            $id
        ]);
        
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Product updated successfully!'];
        header("Location: index.php?page=admin-products");
        exit();
    }
}

// --- 3. ACTION: DELETE ---
if ($page === 'admin-product-delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Product deleted successfully.'];
    header("Location: index.php?page=admin-products");
    exit();
}

$where = " WHERE 1=1";
$params = [];
if (!empty($_GET['search'])) { $where .= " AND p.name LIKE ?"; $params[] = "%" . $_GET['search'] . "%"; }
if (isset($_GET['status']) && $_GET['status'] !== '') { $where .= " AND p.status = ?"; $params[] = (int)$_GET['status']; }
if (!empty($_GET['category_id'])) { $where .= " AND p.category_id = ?"; $params[] = (int)$_GET['category_id']; }
if (!empty($_GET['brand_id'])) { $where .= " AND p.brand_id = ?"; $params[] = (int)$_GET['brand_id']; }

// --- 5. DATA: PAGINATION MATH ---
$perPage = 8;
$currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($currentPage - 1) * $perPage;

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM products p $where");
$totalStmt->execute($params);
$totalItems = $totalStmt->fetchColumn();
$totalPages = ceil($totalItems / $perPage);

// --- 6. DATA: FETCH SINGLE PRODUCT (FOR EDIT/SHOW) ---
// We move this BEFORE the main list fetch and use JOINs
if (($page === 'admin-product-edit' || $page === 'admin-product-show') && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT p.*, 
                   c.name as category_name, 
                   sc.name as sub_category_name, 
                   b.name as brand_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.id = ?";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}

// --- 7. DATA: FETCH ALL PRODUCTS (FOR INDEX TABLE) ---
$sort = $_GET['sort'] ?? 'latest';
$orderBy = " ORDER BY p.id DESC"; 
if ($sort === 'az') $orderBy = " ORDER BY p.name ASC";
if ($sort === 'oldest') $orderBy = " ORDER BY p.id ASC";

$finalQuery = "SELECT p.*, c.name as category_name, sc.name as sub_category_name, b.name as brand_name 
               FROM products p
               LEFT JOIN categories c ON p.category_id = c.id
               LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id
               LEFT JOIN brands b ON p.brand_id = b.id
               $where $orderBy LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($finalQuery);
$stmt->execute($params);
$products = $stmt->fetchAll();

// --- 8. DATA: DROPDOWNS ---
$categories = $pdo->query("SELECT id, name FROM categories WHERE status = 1 ORDER BY name ASC")->fetchAll();
$sub_categories = $pdo->query("SELECT id, name, category_id FROM sub_categories WHERE status = 1 ORDER BY name ASC")->fetchAll();
$brands = $pdo->query("SELECT id, name FROM brands WHERE status = 1 ORDER BY name ASC")->fetchAll();

// --- 9. ACTION: BULK ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'delete') {
    if (!empty($_POST['ids'])) {
        $ids = $_POST['ids'];
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("DELETE FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        
        $_SESSION['flash'] = ['type' => 'success', 'message' => count($ids) . ' products deleted.'];
        header("Location: index.php?page=admin-products");
        exit();
    }
}