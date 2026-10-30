<?php
require_once '../resources/views/admin/layouts/header.view.php'; ?>

<main>
    <a href="index.php?page=admin-brands" class="btn" style="margin-bottom:12px;">&larr; Back to Brands</a>

    <?php if (empty($brand)): ?>
        <div class="admin-form" style="text-align:center;">
            <h2>Brand not found</h2>
            <p>The brand you're looking for does not exist or was removed.</p>
        </div>
    <?php else: ?>
        <div class="admin-form">
            <div style="display:flex;gap:24px;align-items:flex-start;">
                <div style="flex:1;display:flex;flex-direction:column;gap:12px;">
                    <h2 style="margin:0;">Brand: <?= htmlspecialchars($brand['name']) ?></h2>
                    <div style="margin-top:8px;color:#444;">Description: <?= nl2br(htmlspecialchars($brand['description'] ?? '')) ?></div>
                    <div style="margin-top:8px;color:#444;">Created At: <?= htmlspecialchars($brand['created_at'] ?? '') ?></div>
                    <div style="margin-top:8px;color:#444;">Updated At: <?= htmlspecialchars($brand['updated_at'] ?? '') ?></div>
                    <span class="status-pill status-<?= $brand['status'] == 1 ? 'active' : 'inactive' ?>">
                        <?= $brand['status'] == 1 ? 'Active' : 'Inactive' ?>
                    </span>

                    <div class="form-actions" style="margin-top:18px;">
                        <a href="index.php?page=admin-brand-edit&id=<?= $brand['id'] ?>" class="btn btn-edit">Edit Brand</a>
                        <a href="index.php?page=admin-brand-delete&id=<?= $brand['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this brand?');">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>
