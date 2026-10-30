<?php
require_once '../resources/views/admin/layouts/header.view.php';
/** @var array $parent_categories */
/** @var array $subcategory */
$parent_categories = $parent_categories ?? [];
$subcategory = $subcategory ?? [];
?>

<main>
    <a href="index.php?page=admin-subcategories" class="btn" style="margin-bottom:12px;">&larr; Back to Sub Categories</a>

    <?php if (empty($subcategory)): ?>
        <div class="admin-form" style="text-align:center;">
            <h2>Sub Category not found</h2>
            <p>The sub category you're looking for does not exist or was removed.</p>
        </div>
    <?php else: ?>
        <div class="admin-form">
            <?php
                $parent_name = '';
                if (!empty($subcategory) && !empty($parent_categories) && is_array($parent_categories)) {
                    $parentId = $subcategory['category_id'] ?? $subcategory['parent_id'] ?? null;
                    foreach ($parent_categories as $cat) {
                        if ($cat['id'] == $parentId) {
                            $parent_name = $cat['name'];
                            break;
                        }
                    }
                }
            ?>
            <div style="display:flex;gap:24px;align-items:flex-start;">
                <div style="flex:1;display:flex;flex-direction:column;gap:12px;">
                    <h2 style="margin:0;">Sub Category: <?= htmlspecialchars($subcategory['name']) ?></h2>
                    <h2 style="margin:0;">Parent Category: <?= htmlspecialchars($parent_name) ?></h2>
                    <div style="margin-top:8px;color:#444;">Description: <?= nl2br(htmlspecialchars($subcategory['description'] ?? '')) ?></div>
                    <div style="margin-top:8px;color:#444;">Created At: <?= htmlspecialchars($subcategory['created_at'] ?? '') ?></div>
                    <div style="margin-top:8px;color:#444;">Updated At: <?= htmlspecialchars($subcategory['updated_at'] ?? '') ?></div>
                    <span class="status-pill status-<?= $subcategory['status'] == 1 ? 'active' : 'inactive' ?>">
                        <?= $subcategory['status'] == 1 ? 'Active' : 'Inactive' ?>
                    </span>

                    <div class="form-actions" style="margin-top:18px;">
                        <a href="index.php?page=admin-subcategory-edit&id=<?= $subcategory['id'] ?>" class="btn btn-edit">Edit Sub Category</a>
                        <a href="index.php?page=admin-subcategory-delete&id=<?= $subcategory['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this sub category?');">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>
