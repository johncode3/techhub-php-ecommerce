<?php
protectAdmin();

// 1. FILTER SETTINGS
$where = " WHERE 1=1";
$params = [];

// Real Search for Usernames or Emails
if (!empty($_GET['search'])) {
    $where .= " AND (username LIKE ? OR email LIKE ?)";
    $params[] = "%" . $_GET['search'] . "%";
    $params[] = "%" . $_GET['search'] . "%";
}

// Sorting
$sort = $_GET['sort'] ?? 'latest';
$orderBy = " ORDER BY id DESC"; 
if ($sort === 'az') $orderBy = " ORDER BY username ASC";
if ($sort === 'oldest') $orderBy = " ORDER BY id ASC";
if ($sort === 'za') $orderBy = " ORDER BY username DESC";


// Filter by Role (Admin vs Client)
if (!empty($_GET['role'])) {
    $where .= " AND role = ?";
    $params[] = $_GET['role'];
}

// 2. PAGINATION (Reuse your math!)
$perPage = 10;
$currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($currentPage - 1) * $perPage;

// 2b. HANDLE DELETE ACTION (simple GET action from the admin UI)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delId = (int)$_GET['id'];
    $currentUserId = (int)($_SESSION['user_id'] ?? 0);
    if ($delId === $currentUserId) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'You cannot delete your own account.'];
        header("Location: index.php?page=admin-users");
        exit();
    }
    $dstmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $dstmt->execute([$delId]);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'User deleted.'];
    header("Location: index.php?page=admin-users");
    exit();
}

// Count total
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM users $where");
$totalStmt->execute($params);
$totalItems = $totalStmt->fetchColumn();
$totalPages = ceil($totalItems / $perPage);

// 3. THE FETCH (The real data)
$query = "SELECT id, username, email, role, created_at FROM users $where $orderBy LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();