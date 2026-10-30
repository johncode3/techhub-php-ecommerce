<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $parent_categories */
/** @var array $sub_categories */
$parent_categories = $parent_categories ?? [];
$sub_categories = $sub_categories ?? [];
?>

<main>
    <h1>Sub Categories Management</h1>

    <a href="index.php?page=admin-subcategory-create" class="btn btn-add">
        <i class="fas fa-plus"></i> Add New Sub Category
    </a>

    <form method="GET" action="index.php" class="product-filters filter-bar">
        <input type="hidden" name="page" value="admin-subcategories">

        <div class="filter-search">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search sub category..." class="filter-input">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </div>

        <select name="category_id" class="filter-input" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php foreach ($parent_categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

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

        <a href="index.php?page=admin-subcategories" class="btn btn-delete">Reset</a>
    </form>

    <form method="post" action="index.php?page=admin-subcategories" id="bulk-action-form">
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
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Sub-Category</th>
                        <th>Parent Category</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sub_categories as $sub): ?>
                    <tr>
                        <td><input type="checkbox" class="row-checkbox" name="ids[]" value="<?= $sub['id'] ?>"></td>
                        <td>
                            <a href="index.php?page=admin-subcategory-show&id=<?= $sub['id'] ?>" class="table-link" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;">
                                <strong><?= htmlspecialchars($sub['name']) ?></strong>
                            </a>
                        </td>
                    
                        <td>
                            <span class="category-badge"><?= htmlspecialchars($sub['parent_name']) ?></span>
                        </td>

                        <td><?= htmlspecialchars($sub['description']) ?></td>
                        
                        <td>
                            <span class="status-pill status-<?= $sub['status'] == 1 ? 'active' : 'inactive' ?>">
                                <?= $sub['status'] == 1 ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        
                        <td>
                            <a href="index.php?page=admin-subcategory-edit&id=<?= $sub['id'] ?>" class="btn btn-edit"><i class="fas fa-edit"></i></a>
                            <a href="index.php?page=admin-subcategory-delete&id=<?= $sub['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>

    <?php include '../resources/views/admin/partials/pagination.view.php'; ?>

</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>