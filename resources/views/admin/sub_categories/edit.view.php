<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $parent_categories */
/** @var array $subcategory */
$parent_categories = $parent_categories ?? [];
$subcategory = $subcategory ?? [];
?>

<main>
    <h1>Edit Sub Category</h1>
    <form class="admin-form" action="index.php?page=admin-subcategory-edit&id=<?= intval($subcategory['id']) ?>" method="POST">
        <input type="hidden" name="subcategory_id" value="<?= intval($subcategory['id']) ?>">
        <div class="form-row">
            <div class="form-group" style="margin-top:15px;">
                <label>Sub Category Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($subcategory['name'] ?? '') ?>" required class="filter-input">
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label>Parent Category</label>
                <select name="category_id" class="filter-input" required>
                    <option value="">Select parent category</option>
                    <?php foreach ($parent_categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (isset($subcategory['category_id']) && $subcategory['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-top:15px;">
                <label>Description</label>
                <textarea name="description" class="filter-input" style="height:100px;"><?= htmlspecialchars($subcategory['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label>Status</label>
                <select name="status" class="filter-input">
                    <option value="1" <?= ($subcategory['status'] == 1) ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= ($subcategory['status'] == 0) ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="update_subcategory" class="btn btn-add">Update Sub Category</button>
            <a href="index.php?page=admin-subcategories" class="btn btn-delete">Cancel</a>
        </div>
    </form>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>