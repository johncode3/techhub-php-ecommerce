<?php
require_once '../resources/views/admin/layouts/header.view.php'; ?>

<main>
    <a href="index.php?page=admin-categories" class="btn" style="margin-bottom:12px;">&larr; Back to Categories</a>

    <?php if (empty($category)): ?>
        <div class="admin-form" style="text-align:center;">
            <h2>Category not found</h2>
            <p>The category you're looking for does not exist or was removed.</p>
        </div>
    <?php else: ?>
        <div class="admin-form">
            <div style="display:flex;gap:24px;align-items:flex-start;">
                <div style="flex:1;display:flex;flex-direction:column;gap:12px;">
                    <h2 style="margin:0;">Category: <?= htmlspecialchars($category['name']) ?></h2>
                    <div style="margin-top:8px;color:#444;">Description: <?= nl2br(htmlspecialchars($category['description'] ?? '')) ?></div>
                    <div style="margin-top:8px;color:#444;">Created At: <?= htmlspecialchars($category['created_at'] ?? '') ?></div>
                    <div style="margin-top:8px;color:#444;">Updated At: <?= htmlspecialchars($category['updated_at'] ?? '') ?></div>
                    <span class="status-pill status-<?= $category['status'] == 1 ? 'active' : 'inactive' ?>">
                        <?= $category['status'] == 1 ? 'Active' : 'Inactive' ?>
                    </span>

                    <div class="form-actions" style="margin-top:18px;">
                        <a href="index.php?page=admin-category-edit&id=<?= $category['id'] ?>" class="btn btn-edit">Edit Category</a>
                        <a href="index.php?page=admin-category-delete&id=<?= $category['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this category?');">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>
