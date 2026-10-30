<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>

<main>
    <h1>Add New Category</h1>
    <form class="admin-form" action="index.php?page=admin-category-create" method="POST" >
        <div class="form-row">
            <div class="form-group" style="margin-top:15px;">
                <label>Category Name</label>
                <input type="text" name="name" required class="filter-input" placeholder="e.g. Laptops">
            </div>
            <div class="form-group" style="margin-top:15px;">
                <label>Description</label>
                <textarea name="description" class="filter-input" style="height:100px;"></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="add_category" class="btn btn-add">Add Category</button>
            <a href="index.php?page=admin-categories" class="btn btn-delete">Cancel</a>
        </div>
    </form>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>