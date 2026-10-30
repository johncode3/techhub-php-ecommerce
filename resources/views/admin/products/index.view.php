<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $products */
/** @var array $categories */
/** @var array $sub_categories */
/** @var array $brands */
$products = $products ?? [];
$categories = $categories ?? [];
$sub_categories = $sub_categories ?? [];
$brands = $brands ?? [];
?>

<main>
    <h1>Products Management</h1>
    <a href="index.php?page=admin-product-create" class="btn btn-add">
        <i class="fas fa-plus"></i> Add New Product
    </a>

    <form method="GET" action="index.php" class="product-filters filter-bar">
        <input type="hidden" name="page" value="admin-products">

        <div class="filter-search">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search product..." class="filter-input">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </div>

        <select name="status" class="filter-input" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="1" <?= (isset($_GET['status']) && $_GET['status'] === '1') ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : '' ?>>Inactive</option>
        </select>

        <select name="category_id" class="filter-input" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="brand_id" class="filter-input" onchange="this.form.submit()">
            <option value="">All Brands</option>
            <?php foreach ($brands as $brand): ?>
                <option value="<?= $brand['id'] ?>" <?= (isset($_GET['brand_id']) && $_GET['brand_id'] == $brand['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($brand['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="sort" class="filter-input" onchange="this.form.submit()">
            <option value="latest" <?= ($_GET['sort'] ?? '') === 'latest' ? 'selected' : '' ?>>Newest First</option>
            <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
            <option value="az" <?= ($_GET['sort'] ?? '') === 'az' ? 'selected' : '' ?>>Name A-Z</option>
            <option value="za" <?= ($_GET['sort'] ?? '') === 'za' ? 'selected' : '' ?>>Name Z-A</option>
        </select>

        <a href="index.php?page=admin-products" class="btn btn-delete">Reset</a>
    </form>

    <form method="post" action="index.php?page=admin-products" id="bulk-action-form">
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
                        <th>Product</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Badge</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 50px; color: #999;">
                                <i class="fas fa-box-open fa-3x" style="margin-bottom: 10px;"></i>
                                <p>No products found matching your filters.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><input type="checkbox" class="row-checkbox" name="ids[]" value="<?= $product['id'] ?>"></td>
                                <td>
                                    <?php $img = $product['main_image'] ?? ''; ?>
                                    <a href="index.php?page=admin-product-show&id=<?= $product['id'] ?>" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;">
                                        <img src="<?= htmlspecialchars($img !== '' ? $img : 'https://via.placeholder.com/50') ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius:6px;">
                                        <strong><?= htmlspecialchars($product['name']) ?></strong>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($product['brand_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($product['category_name'] ?? '') ?></td>
                                <td>$<?= number_format($product['price'], 2) ?></td>
                                <td><?= $product['stock'] ?></td>
                                <td>
                                    <span class="status-pill status-<?= $product['status'] == 1 ? 'active' : 'inactive' ?>">
                                        <?= $product['status'] == 1 ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                        $price = (float)$product['price'];
                                        $origPrice = (float)($product['original_price'] ?? 0);
                                        $isNew = !empty($product['is_new']);
                                        $isSale = ($origPrice > 0 && $origPrice > $price);
                                        $pct = $isSale ? (int)round((($origPrice - $price) / $origPrice) * 100) : 0;
                                    ?>
                                    <div class="badge-stack">
                                        <?php if ($isNew): ?>
                                            <span class="badge badge-new">New</span>
                                        <?php endif; ?>
                                        <?php if ($isSale): ?>
                                            <span class="badge badge-sale">Sale -<?= $pct ?>%</span>
                                        <?php endif; ?>
                                        <?php if (!$isNew && !$isSale): ?>
                                            <span class="badge badge-default">—</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="index.php?page=admin-product-edit&id=<?= $product['id'] ?>" class="btn btn-edit"><i class="fas fa-edit"></i></a>
                                    <a href="index.php?page=admin-product-delete&id=<?= $product['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>

    <?php require_once '../resources/views/admin/partials/pagination.view.php'; ?>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>
