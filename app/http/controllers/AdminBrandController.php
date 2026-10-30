<?php
protectAdmin();
/** @var array $page */

// Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_brand'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    
    if (empty($name)) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Name is required!'];
        header("Location: index.php?page=admin-brand-create");
        exit();
    }

    $check = $pdo->prepare("SELECT id FROM brands WHERE name = ?");
    $check->execute([$name]);

    if ($check->rowCount() > 0) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Brand name already exists!'];
        header("Location: index.php?page=admin-brand-create");
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO brands (name, description, status) VALUES (?, ?, 1)");
    $stmt->execute([$name, $desc]);
    
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Brand created successfully!'];
    header("Location: index.php?page=admin-brands");
    exit();
}

// Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_brand'])) {
    $id = (int)$_POST['brand_id'];
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    
    $status = (int)$_POST['status']; 
    
    if ($id > 0 && !empty($name)) {
        $stmt = $pdo->prepare("UPDATE brands SET name = ?, description = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $desc, $status, $id]);
        
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Brand updated successfully!'];
        header("Location: index.php?page=admin-brands");
        exit();
    }
}

// Delete
if ($page === 'admin-brand-delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM brands WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Brand deleted permanently.'];
    header("Location: index.php?page=admin-brands");
    exit();
}

// Details
if ($page === 'admin-brand-show' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM brands WHERE id = ?");
    $stmt->execute([$id]);
    $brand = $stmt->fetch();
}
// Filters
$where = " WHERE 1=1";
$params = [];

// Handle Search
if (!empty($_GET['search'])) {
    $where .= " AND name LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
}

// Handle Status
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $where .= " AND status = ?";
    $params[] = (int)$_GET['status'];
}

// Pagination
$perPage = 5;
$currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($currentPage - 1) * $perPage;

// Count after filters
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM brands $where");
$totalStmt->execute($params);
$totalItems = $totalStmt->fetchColumn();
$totalPages = ceil($totalItems / $perPage);

// Sorting
$sort = $_GET['sort'] ?? 'latest';
$orderBy = " ORDER BY id DESC";
if ($sort === 'az') $orderBy = " ORDER BY name ASC";
if ($sort === 'oldest') $orderBy = " ORDER BY id ASC";
if ($sort === 'za') $orderBy = " ORDER BY name DESC";

// Query
$finalQuery = "SELECT * FROM brands $where $orderBy LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($finalQuery);
$stmt->execute($params);
$brands = $stmt->fetchAll();


// Data (Edit/Show)
if (($page === 'admin-brand-edit' || $page === 'admin-brand-show') && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM brands WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $brand = $stmt->fetch();
}

// Bulk Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'delete') {
    if (!empty($_POST['ids'])) {
        $ids = $_POST['ids'];
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("DELETE FROM brands WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        
        $_SESSION['flash'] = ['type' => 'success', 'message' => count($ids) . ' brands deleted.'];
        header("Location: index.php?page=admin-brands");
        exit();
    }
}