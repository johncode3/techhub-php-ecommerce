
<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $brand */
$brand = $brand ?? [];
?>

<main>
    <h1>Edit Brand</h1>
    <form class="admin-form" action="index.php?page=admin-brand-edit&id=<?= intval($brand['id']) ?>" method="POST">
        <input type="hidden" name="brand_id" value="<?= intval($brand['id']) ?>">
        <div class="form-row">
            <div class="form-group" style="margin-top:15px;">
                <label>Brand Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($brand['name'] ?? '') ?>" required class="filter-input" placeholder="e.g. Dell, Apple, Samsung">
            </div>
            <div class="form-group" style="margin-top:15px;">
                <label>Description</label>
                <textarea name="description" class="filter-input" style="height:100px;"><?= htmlspecialchars($brand['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label>Status</label>
                <select name="status" class="filter-input">
                    <option value="1" <?= ($brand['status'] == 1) ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= ($brand['status'] == 0) ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="update_brand" class="btn btn-add">Update Brand</button>
            <a href="index.php?page=admin-brands" class="btn btn-delete">Cancel</a>
        </div>
    </form>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>