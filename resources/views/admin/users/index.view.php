<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $users */
$users = $users ?? [];
?>
<main>
    <h1>User Management</h1>
    <p>Manage administrative and customer accounts.</p>

        <form method="GET" action="index.php" class="product-filters filter-bar">
        <input type="hidden" name="page" value="admin-users">

        <div class="filter-search">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search user..." class="filter-input">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </div>

        <select name="role" class="filter-input" onchange="this.form.submit()">
            <option value="">All Roles</option>
            <option value="admin" <?= (isset($_GET['role']) && $_GET['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
            <option value="customer" <?= (isset($_GET['role']) && $_GET['role'] === 'customer') ? 'selected' : '' ?>>Customer</option>
        </select>

        <select name="sort" class="filter-input" onchange="this.form.submit()">
            <option value="latest" <?= ($_GET['sort'] ?? '') === 'latest' ? 'selected' : '' ?>>Newest First</option>
            <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
            <option value="az" <?= ($_GET['sort'] ?? '') === 'az' ? 'selected' : '' ?>>Name A-Z</option>
            <option value="za" <?= ($_GET['sort'] ?? '') === 'za' ? 'selected' : '' ?>>Name Z-A</option>
        </select>

        <a href="index.php?page=admin-users" class="btn btn-delete">Reset</a>
    </form>
    <div class="table-responsive admin-table-responsive">
        <table class="admin-table products-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>#<?= $user['id'] ?></td>
                    <td><strong><?= htmlspecialchars($user['username']) ?></strong></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <!-- DYNAMIC ROLE BADGE -->
                        <span class="status-pill <?= $user['role'] === 'admin' ? 'status-active' : 'status-inactive' ?>" style="text-transform: uppercase; font-size: 10px;">
                            <?= htmlspecialchars($user['role']) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                    <td>
                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                            <a href="index.php?page=admin-users&action=delete&id=<?= $user['id'] ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('Delete this user forever?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php else: ?>
                            <span style="color: #999; font-size: 12px;">(You)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>