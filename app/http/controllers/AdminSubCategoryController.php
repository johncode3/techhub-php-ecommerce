<?php
protectAdmin();
/** @var array $sub_categories */
/** @var array $parent_categories */
/** @var array $page */
// Store
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subcategory'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $category_id = (int)$_POST['category_id'];

    if (empty($name) || $category_id <= 0) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Name and Parent Category are required!'];
        header("Location: index.php?page=admin-subcategory-create");
        exit();
    }

    $check = $pdo->prepare("SELECT id FROM sub_categories WHERE name = ?");
    $check->execute([$name]);

    if ($check->rowCount() > 0) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Subcategory name already exists!'];
        header("Location: index.php?page=admin-subcategory-create");
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO sub_categories (name, description, category_id, status) VALUES (?, ?, ?, 1)");
    $stmt->execute([$name, $desc, $category_id]);
    
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Subcategory created successfully!'];
    header("Location: index.php?page=admin-subcategories");
    exit();
}

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_subcategory'])) {
    $id = (int)$_POST['subcategory_id']; // id
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $status = (int)$_POST['status'];
    
    if ($id > 0 && !empty($name) && $category_id > 0) {
        $stmt = $pdo->prepare("UPDATE sub_categories SET name = ?, description = ?, category_id = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $desc, $category_id, $status, $id]);
        
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Subcategory updated!'];
        header("Location: index.php?page=admin-subcategories");
        exit();
    }
}

// Delete & Bulk Delete
// Single Delete
if ($page === 'admin-subcategory-delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM sub_categories WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Deleted!'];
    header("Location: index.php?page=admin-subcategories");
    exit();
}

// Bulk Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'delete') {
    if (!empty($_POST['ids'])) {
        $ids = $_POST['ids']; // Array of IDs
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("DELETE FROM sub_categories WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Selected items removed.'];
        header("Location: index.php?page=admin-subcategories");
        exit();
    }
}

// Data fetching
// Filter logic
$where = " WHERE 1=1"; 
$params = [];

if (!empty($_GET['search'])) {
    $where .= " AND sc.name LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
}
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $where .= " AND sc.status = ?";
    $params[] = (int)$_GET['status'];
}
// filter by parent category
if (isset($_GET['category_id']) && $_GET['category_id'] !== '') {
    $where .= " AND sc.category_id = ?";
    $params[] = (int)$_GET['category_id'];
}
// sorting
$sort = $_GET['sort'] ?? 'latest';
$orderBy = " ORDER BY id DESC";
if ($sort === 'az') $orderBy = " ORDER BY name ASC";
if ($sort === 'oldest') $orderBy = " ORDER BY id ASC";
if ($sort === 'za') $orderBy = " ORDER BY name DESC";
// Pagination
$perPage = 5;
$currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($currentPage - 1) * $perPage;

// Total items/count
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM sub_categories sc $where");
$totalStmt->execute($params);
$totalItems = $totalStmt->fetchColumn();
$totalPages = ceil($totalItems / $perPage);

// Join: include parent name
$sort = $_GET['sort'] ?? 'latest';
$orderBy = " ORDER BY sc.id DESC";
if ($sort === 'az') $orderBy = " ORDER BY sc.name ASC";
if ($sort === 'oldest') $orderBy = " ORDER BY sc.id ASC";
if ($sort === 'za') $orderBy = " ORDER BY sc.name DESC";
$finalQuery = "SELECT sc.*, c.name AS parent_name 
               FROM sub_categories sc 
               JOIN categories c ON sc.category_id = c.id 
               $where $orderBy LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($finalQuery);
$stmt->execute($params);
$sub_categories = $stmt->fetchAll();

// Parent categories for form
$parent_categories = $pdo->query("SELECT id, name FROM categories WHERE status = 1 ORDER BY name ASC")->fetchAll();

// Fetch single subcategory for edit/show
if (isset($_GET['id']) && ($page === 'admin-subcategory-edit' || $page === 'admin-subcategory-show')) {
    $stmt = $pdo->prepare("SELECT * FROM sub_categories WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $subcategory = $stmt->fetch();
}