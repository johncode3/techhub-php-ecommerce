<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $inventoryProducts */
/** @var array $inventoryCategories */
/** @var array $inventoryBrands */
/** @var array $inventoryStats */
$inventoryProducts = $inventoryProducts ?? [];
$inventoryCategories = $inventoryCategories ?? [];
$inventoryBrands = $inventoryBrands ?? [];
$inventoryStats = $inventoryStats ?? ['total_skus' => 0, 'low_stock' => 0, 'out_of_stock' => 0, 'inventory_value' => '0.00'];
$lowStockThreshold = 5;
?>

<main>
    <h1>Inventory Management</h1>
    <p>Track stock levels and product availability.</p>

    <div class="stats-grid" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
        <div class="card">
            <i class="fas fa-boxes"></i>
            <h3>Total SKUs</h3>
            <p class="stat-number"><?= (int)$inventoryStats['total_skus'] ?></p>
        </div>
        <div class="card">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>Low Stock</h3>
            <p class="stat-number"><?= (int)$inventoryStats['low_stock'] ?></p>
        </div>
        <div class="card">
            <i class="fas fa-ban"></i>
            <h3>Out of Stock</h3>
            <p class="stat-number"><?= (int)$inventoryStats['out_of_stock'] ?></p>
        </div>
        <div class="card">
            <i class="fas fa-dollar-sign"></i>
            <h3>Inventory Value</h3>
            <p class="stat-number">$<?= htmlspecialchars($inventoryStats['inventory_value']) ?></p>
        </div>
    </div>

    <form method="GET" action="index.php" class="product-filters filter-bar">
        <input type="hidden" name="page" value="admin-inventory">

        <div class="filter-search">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search product..." class="filter-input">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </div>

        <select name="stock_level" class="filter-input" onchange="this.form.submit()">
            <option value="">All Stock</option>
            <option value="out" <?= (($_GET['stock_level'] ?? '') === 'out') ? 'selected' : '' ?>>Out of Stock</option>
            <option value="low" <?= (($_GET['stock_level'] ?? '') === 'low') ? 'selected' : '' ?>>Low Stock (≤ <?= $lowStockThreshold ?>)</option>
            <option value="in" <?= (($_GET['stock_level'] ?? '') === 'in') ? 'selected' : '' ?>>In Stock</option>
        </select>

        <select name="status" class="filter-input" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="1" <?= (($_GET['status'] ?? '') === '1') ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= (($_GET['status'] ?? '') === '0') ? 'selected' : '' ?>>Inactive</option>
        </select>

        <select name="category_id" class="filter-input" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php foreach ($inventoryCategories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="brand_id" class="filter-input" onchange="this.form.submit()">
            <option value="">All Brands</option>
            <?php foreach ($inventoryBrands as $brand): ?>
                <option value="<?= $brand['id'] ?>" <?= (isset($_GET['brand_id']) && $_GET['brand_id'] == $brand['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($brand['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <a href="index.php?page=admin-inventory" class="btn btn-delete">Reset</a>
    </form>

    <div class="table-responsive admin-table-responsive">
        <table class="admin-table inventory-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Availability</th>
                    <th>Adjust</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inventoryProducts)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:#888;">
                            No products found for this inventory filter.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inventoryProducts as $product): ?>
                        <?php
                            $stock = (int)($product['stock'] ?? 0);
                            $availabilityClass = 'status-in-stock';
                            $availabilityLabel = 'In Stock';

                            if ($stock <= 0) {
                                $availabilityClass = 'status-out-of-stock';
                                $availabilityLabel = 'Out of Stock';
                            } elseif ($stock <= $lowStockThreshold) {
                                $availabilityClass = 'status-low-stock';
                                $availabilityLabel = 'Low Stock';
                            }
                        ?>
                        <tr>
                            <td>
                                <?php $img = $product['main_image'] ?? ''; ?>
                                <a href="index.php?page=admin-product-show&id=<?= $product['id'] ?>" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;">
                                    <img src="<?= htmlspecialchars($img !== '' ? $img : 'https://via.placeholder.com/50') ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius:6px;">
                                    <strong><?= htmlspecialchars($product['name']) ?></strong>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($product['category_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($product['brand_name'] ?? '') ?></td>
                            <td>$<?= number_format((float)($product['price'] ?? 0), 2) ?></td>
                            <td><?= $stock ?></td>
                            <td>
                                <span class="status-pill <?= $availabilityClass ?>">
                                    <?= $availabilityLabel ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="index.php?page=admin-inventory" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                                    <input type="hidden" name="inventory_adjust" value="1">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="redirect" value="index.php?page=admin-inventory">
                                    <select name="adjust_action" class="filter-input" style="min-width:110px;">
                                        <option value="set">Set Stock</option>
                                        <option value="add">Add Qty</option>
                                        <option value="sub">Subtract Qty</option>
                                    </select>
                                    <input type="number" name="adjust_qty" min="1" step="1" value="1" class="filter-input" style="width:90px;">
                                    <button type="submit" class="btn btn-edit" title="Apply stock change">
                                        Apply
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>
