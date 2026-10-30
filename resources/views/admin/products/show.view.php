<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $product */
$product = $product ?? [];

// Logic: Prepare the data before HTML starts
$img = $product['main_image'] ?? '';
$thumbs = !empty($product['thumbnails']) ? json_decode($product['thumbnails'], true) : [];
$originalPrice = (float)($product['original_price'] ?? 0);
$price = (float)($product['price'] ?? 0);
$discount = ($originalPrice > $price) ? ($originalPrice - $price) : 0;
?>

<main>
    <a href="index.php?page=admin-products" class="btn" style="margin-bottom:12px;">&larr; Back to Products</a>

    <?php if (empty($product)): ?>
        <div class="admin-form" style="text-align:center; padding: 50px;">
            <i class="fas fa-search fa-3x" style="color:#ccc;"></i>
            <h2>Product Not Found</h2>
        </div>
    <?php else: ?>
        <div class="admin-form">
            <div style="display:flex; gap:40px; align-items:flex-start; flex-wrap: wrap;">
                
                <!-- LEFT SIDE: MEDIA (Images) -->
                <div style="flex: 0 0 300px;">
                    <div class="main-preview" style="border: 1px solid #eee; border-radius: 12px; overflow: hidden; background: #fff;">
                        <img src="<?= htmlspecialchars($img !== '' ? $img : 'assets/images/placeholder.png') ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             style="width:100%; height:300px; object-fit: cover;">
                    </div>

                    <?php if (!empty($thumbs)): ?>
                        <div style="display:flex; gap:10px; margin-top:15px; flex-wrap: wrap;">
                            <?php foreach ($thumbs as $thumb): ?>
                                <div style="width:65px; height:65px; border:1px solid #ddd; border-radius:8px; overflow:hidden;">
                                    <img src="<?= htmlspecialchars($thumb) ?>" style="width:100%; height:100%; object-fit:cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- RIGHT SIDE: PRODUCT INFO -->
                <div style="flex:1; min-width: 300px; display:flex; flex-direction:column; gap:15px;">
                    
                    <!-- Title and Status -->
                    <div>
                        <span class="status-pill status-<?= ($product['status'] == 1) ? 'active' : 'inactive' ?>" style="margin-bottom: 10px; display:inline-block;">
                            <?= ($product['status'] == 1) ? 'Active' : 'Inactive' ?>
                        </span>
                        <?php if(!empty($product['is_new'])): ?>
                            <span class="status-pill" style="background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7;">NEW ARRIVAL</span>
                        <?php endif; ?>
                        <h2 style="font-size: 2rem; margin: 5px 0;"><?= htmlspecialchars($product['name']) ?></h2>
                        <p style="color: #666; font-size: 0.9rem;">
                            ID: #<?= $product['id'] ?> | Category: <strong><?= htmlspecialchars($product['category_name']) ?></strong> &raquo; <?= htmlspecialchars($product['sub_category_name']) ?>
                        </p>
                    </div>

                    <hr style="border:0; border-top: 1px solid #eee;">

                    <!-- Pricing & Inventory -->
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="font-size: 0.8rem; color: #999; text-transform: uppercase;">Current Price</label>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #2c3e50;">$<?= number_format($price, 2) ?></div>
                            <?php if ($discount > 0): ?>
                                <div style="font-size: 0.9rem; color: #e74c3c;">
                                    <del style="color: #999;">$<?= number_format($originalPrice, 2) ?></del> 
                                    Save $<?= number_format($discount, 2) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; color: #999; text-transform: uppercase;">Stock Status</label>
                            <div style="font-size: 1.2rem; font-weight: 600;">
                                <i class="fas fa-warehouse"></i> <?= intval($product['stock']) ?> Units
                            </div>
                            <div style="font-size: 0.85rem; color: #666;">
                                <?= number_format((float)$product['rating'], 1) ?> ⭐ (<?= intval($product['review_count']) ?> Reviews)
                            </div>
                        </div>
                    </div>

                    <!-- Variations (Color/Storage) -->
                    <?php if (!empty($product['color']) || !empty($product['storage'])): ?>
                    <div style="background: #fcfcfc; padding: 15px; border-radius: 8px; border: 1px solid #f0f0f0;">
                        <?php if (!empty($product['color'])): ?>
                            <div style="margin-bottom: 10px;">
                                <strong style="font-size: 0.85rem;">Colors:</strong>
                                <div style="display:flex; gap:5px; margin-top:5px;">
                                    <?php foreach(explode(',', $product['color']) as $c): ?>
                                        <span class="badge" style="background:#fff; border:1px solid #ddd; padding: 2px 10px; border-radius:4px; font-size:11px;"><?= htmlspecialchars(trim($c)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($product['storage'])): ?>
                            <div>
                                <strong style="font-size: 0.85rem;">Storage:</strong>
                                <div style="display:flex; gap:5px; margin-top:5px;">
                                    <?php foreach(explode(',', $product['storage']) as $s): ?>
                                        <span class="badge" style="background:#e3f2fd; color:#1976d2; padding: 2px 10px; border-radius:4px; font-size:11px; font-weight:bold;"><?= htmlspecialchars(trim($s)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Description -->
                    <div style="margin-top: 10px;">
                        <strong style="font-size: 0.85rem;">Description:</strong>
                        <p style="color: #444; line-height: 1.6; margin-top: 5px;"><?= nl2br(htmlspecialchars($product['description'] ?? 'No description provided.')) ?></p>
                    </div>

                    <!-- ACTIONS -->
                    <div class="form-actions" style="margin-top: 10px; padding-top: 20px; border-top: 1px solid #eee;">
                        <a href="index.php?page=admin-product-edit&id=<?= $product['id'] ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit Product</a>
                        <a href="index.php?page=admin-product-delete&id=<?= $product['id'] ?>" class="btn btn-delete" onclick="return confirm('Permanent delete?')"><i class="fas fa-trash"></i> Delete</a>
                    </div>
                </div>
            </div>

            <!-- FULL WIDTH: TECHNICAL SPECS TABLE -->
            <?php if (!empty($product['specifications'])): ?>
                <div style="margin-top: 40px;">
                    <h3 style="margin-bottom: 15px; display:flex; align-items:center; gap:10px;">
                        <i class="fas fa-microchip"></i> Technical Specifications
                    </h3>
                    <table class="admin-table" style="width: 100%; border: 1px solid #eee; border-radius: 8px; overflow: hidden;">
                        <tbody>
                            <?php 
                            foreach (explode('|', $product['specifications']) as $line): 
                                $parts = explode(':', $line);
                                if (count($parts) == 2):
                            ?>
                                <tr>
                                    <td style="background: #f9f9f9; width: 25%; font-weight: 600; padding: 12px; color: #555;">
                                        <?= htmlspecialchars(trim($parts[0])) ?>
                                    </td>
                                    <td style="padding: 12px;"><?= htmlspecialchars(trim($parts[1])) ?></td>
                                </tr>
                            <?php endif; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>