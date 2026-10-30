<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $categories */
$categories = $categories ?? [];
?>

<main>
    <h1>Categories Management</h1>
    <a href="index.php?page=admin-category-create" class="btn btn-add">
        <i class="fas fa-plus"></i> Add New Category
    </a>

    <form method="GET" action="index.php" class="product-filters filter-bar">
        <input type="hidden" name="page" value="admin-categories">

        <div class="filter-search">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search category..." class="filter-input">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </div>

        <select name="status" class="filter-input" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="1" <?= (isset($_GET['status']) && $_GET['status'] === '1') ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : '' ?>>Inactive</option>
        </select>

        <select name="sort" class="filter-input" onchange="this.form.submit()">
            <option value="latest" <?= ($_GET['sort'] ?? '') === 'latest' ? 'selected' : '' ?>>Newest First</option>
            <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
            <option value="az" <?= ($_GET['sort'] ?? '') === 'az' ? 'selected' : '' ?>>Name A-Z</option>
            <option value="za" <?= ($_GET['sort'] ?? '') === 'za' ? 'selected' : '' ?>>Name Z-A</option>
        </select>

        <a href="index.php?page=admin-categories" class="btn btn-delete">Reset</a>
    </form>

    <form method="post" action="index.php?page=admin-categories" id="bulk-action-form">
        <div class="product-actions" style="display:flex;align-items:center;gap:10px;margin:12px 0;">
            <select name="bulk_action" id="bulk-action-select">
                <option value="">Bulk actions</option>
                <option value="delete">Delete selected</option>
            </select>
            <button type="submit" id="bulk-apply" class="btn btn-delete" disabled>Apply</button>
        </div>
        <div class="table-responsive admin-table-responsive">
            <table class="admin-table products-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" title="Select All"></th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><input type="checkbox" class="row-checkbox" name="ids[]" value="<?= $cat['id'] ?>"></td>
                        <td >
                            <a href="index.php?page=admin-category-show&id=<?= $cat['id'] ?>" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;">
                                <strong><?= htmlspecialchars($cat['name']) ?></strong>
                            </a>
                        </td>

                        <td><?= htmlspecialchars($cat['description']) ?></td>
                        <td>
                            <span class="status-pill status-<?= $cat['status'] == 1 ? 'active' : 'inactive' ?>">
                                <?= $cat['status'] == 1 ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <a href="index.php?page=admin-category-edit&id=<?= $cat['id'] ?>" class="btn btn-edit"><i class="fas fa-edit"></i></a>
                            <a href="index.php?page=admin-category-delete&id=<?= $cat['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>

    <?php require_once '../resources/views/admin/partials/pagination.view.php'; ?>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>