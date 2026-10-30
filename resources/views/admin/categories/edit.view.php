<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $category */
$category = $category ?? [];
?>
<main>
    <h1>Edit Category</h1>
    <form class="admin-form" action="index.php?page=admin-category-edit&id=<?= intval($category['id']) ?>" method="POST">
        <input type="hidden" name="category_id" value="<?= intval($category['id']) ?>">
        <div class="form-row">
            <div class="form-group" style="margin-top:15px;">
                <label>Category Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($category['name'] ?? '') ?>" required class="filter-input" placeholder="e.g. Laptops">
            </div>
            <div class="form-group" style="margin-top:15px;">
                <label>Description</label>
                <textarea name="description" class="filter-input" style="height:100px;"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label>Status</label>
                <select name="status" class="filter-input">
                    <option value="1" <?= ($category['status'] == 1) ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= ($category['status'] == 0) ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="update_category" class="btn btn-add">Update Category</button>
            <a href="index.php?page=admin-categories" class="btn btn-delete">Cancel</a>
        </div>
    </form>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>