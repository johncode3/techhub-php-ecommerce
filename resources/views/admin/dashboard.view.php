<?php
require_once '../resources/views/admin/layouts/header.view.php';
/** @var array $stats */
$stats = $stats ?? [
    'total_products' => 0,
    'active_products' => 0,
    'low_stock_products' => 0,
    'active_users' => 0,
    'inventory_value' => '0.00',
];
/** @var array $lowStockProducts */
$lowStockProducts = $lowStockProducts ?? [];
/** @var array $recentProducts */
$recentProducts = $recentProducts ?? [];
?>

<main class="admin-main container">
    <section style="display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;margin-bottom:24px;">
        <div>
            <h1 style="margin-bottom:8px;">Admin Dashboard</h1>
            <p style="margin:0;color:#666;max-width:700px;">A live overview of your catalog, stock health, and latest product activity.</p>
        </div>
        <a href="index.php?page=admin-product-create" class="btn btn-add">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </section>

    <section class="stats-grid">
        <div class="card">
            <i class="fas fa-box"></i>
            <h3>Total Products</h3>
            <p class="stat-number"><?= (int)$stats['total_products'] ?></p>
        </div>

        <div class="card">
            <i class="fas fa-circle-check"></i>
            <h3>Active Products</h3>
            <p class="stat-number"><?= (int)$stats['active_products'] ?></p>
        </div>

        <div class="card">
            <i class="fas fa-triangle-exclamation"></i>
            <h3>Low Stock Items</h3>
            <p class="stat-number"><?= (int)$stats['low_stock_products'] ?></p>
        </div>

        <div class="card">
            <i class="fas fa-users"></i>
            <h3>Total Users</h3>
            <p class="stat-number"><?= (int)$stats['active_users'] ?></p>
        </div>
    </section>

    <section style="margin-top:28px;display:grid;grid-template-columns:1.3fr 0.9fr;gap:22px;align-items:start;">
        <div class="admin-form" style="margin:0;">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px;flex-wrap:wrap;">
                <h2 style="margin:0;">Latest Products</h2>
                <span class="status-pill status-active">Inventory Value: $<?= htmlspecialchars($stats['inventory_value']) ?></span>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentProducts)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;padding:28px;color:#888;">No products yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentProducts as $product): ?>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:12px;">
                                            <?php $img = $product['main_image'] ?? ''; ?>
                                            <img src="<?= htmlspecialchars($img !== '' ? $img : 'https://via.placeholder.com/60') ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:44px;height:44px;object-fit:cover;border-radius:8px;">
                                            <div>
                                                <div style="font-weight:700;"><?= htmlspecialchars($product['name']) ?></div>
                                                <div style="font-size:0.85rem;color:#777;">$<?= number_format((float)($product['price'] ?? 0), 2) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($product['brand_name'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars(trim(($product['category_name'] ?? '') . ' / ' . ($product['sub_category_name'] ?? ''), ' /')) ?></td>
                                    <td><?= (int)($product['stock'] ?? 0) ?></td>
                                    <td>
                                        <span class="status-pill status-<?= ((int)($product['status'] ?? 0) === 1) ? 'active' : 'inactive' ?>">
                                            <?= ((int)($product['status'] ?? 0) === 1) ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-form" style="margin:0;">
            <h2 style="margin-top:0;">Stock Alerts</h2>
            <?php if (empty($lowStockProducts)): ?>
                <p style="color:#666;margin-bottom:0;">No low-stock products right now.</p>
            <?php else: ?>
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <?php foreach ($lowStockProducts as $product): ?>
                        <?php
                            $stock = (int)($product['stock'] ?? 0);
                            $label = $stock <= 0 ? 'Out of Stock' : 'Low Stock';
                        ?>
                        <div style="display:flex;gap:12px;align-items:center;padding:12px;border:1px solid #eee;border-radius:10px;">
                            <?php $img = $product['main_image'] ?? ''; ?>
                            <img src="<?= htmlspecialchars($img !== '' ? $img : 'https://via.placeholder.com/50') ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:54px;height:54px;object-fit:cover;border-radius:8px;">
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($product['name']) ?></div>
                                <div style="font-size:0.9rem;color:#777;"><?= htmlspecialchars($product['brand_name'] ?? '-') ?> · <?= htmlspecialchars($product['category_name'] ?? '-') ?></div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-weight:700;"><?= $stock ?></div>
                                <span class="status-pill status-<?= $stock <= 0 ? 'out-of-stock' : 'low-stock' ?>"><?= $label ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>
