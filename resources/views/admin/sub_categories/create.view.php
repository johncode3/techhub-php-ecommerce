<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $parent_categories */
$parent_categories = $parent_categories ?? [];
?>

<main>
    <h1>Add New Sub Category</h1>
    <form class="admin-form" action="index.php?page=admin-subcategory-create" method="POST" >
        <div class="form-row">
            <div class="form-group" style="margin-top:15px;">
                <label>Sub Category Name</label>
                <input type="text" name="name" required class="filter-input" placeholder="e.g. Laptops">
            </div>

            <div class="form-group" style="margin-top:15px;">
                <label>Parent Category</label>
                <select name="category_id" class="filter-input">
                    <option value="">Select a Category</option>
                    <?php foreach ($parent_categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-top:15px;">
                <label>Description</label>
                <textarea name="description" class="filter-input" style="height:100px;"></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="add_subcategory" class="btn btn-add">Add Sub Category</button>
            <a href="index.php?page=admin-subcategories" class="btn btn-delete">Cancel</a>
        </div>
    </form>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>