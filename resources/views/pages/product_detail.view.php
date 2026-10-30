<?php
/** @var array $product */

$product = $product ?? [];
?> 
<main class="container">
    <!-- Breadcrumb Trail -->
    <nav class="breadcrumb">
        <div class="container">
            <a href="index.php?page=home">Home</a>
            <i class="fas fa-chevron-right"></i>
            
            <a href="index.php?page=product">Shop</a>
            <i class="fas fa-chevron-right"></i>
            
            <!-- Category Link -->
            <a href="index.php?page=product&category_id=<?= $product['category_id'] ?>">
                <?= htmlspecialchars($product['category_name']) ?>
            </a>
            <i class="fas fa-chevron-right"></i>

            <!-- Sub-Category Link -->
            <a href="index.php?page=product&sub_category_id=<?= $product['sub_category_id'] ?>">
                <?= htmlspecialchars($product['sub_category_name']) ?>
            </a>
            <i class="fas fa-chevron-right"></i>

            <!-- Current Product (Inactive link) -->
            <span class="current"><?= htmlspecialchars($product['name']) ?></span>
        </div>
    </nav>
    <div class="product-detail-container">
        <div class="product-detail-content">
            <!-- 1. IMAGES SECTION (Looking good!) -->
            <div class="product-images">
                <div class="main-image">
                    <img src="<?= htmlspecialchars($product['main_image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" class="js-lightbox-image">
                </div>
                <div class="thumbnail-strip">
                    <?php
                    $thumbs = [];
                    if (!empty($product['thumbnails']) && is_array($product['thumbnails'])) {
                        $thumbs = $product['thumbnails'];
                    }
                    if (!empty($product['main_image'])) {
                        $mainImage = $product['main_image'];
                        if (!in_array($mainImage, $thumbs, true)) {
                            array_unshift($thumbs, $mainImage);
                        }
                    }
                    ?>
                    <?php foreach ($thumbs as $i => $thumb): ?>
                        <img src="<?= htmlspecialchars($thumb, ENT_QUOTES, 'UTF-8') ?>" class="thumb js-lightbox-thumb<?= $i === 0 ? ' active' : '' ?>" alt="Thumb">
                    <?php endforeach; ?>
                </div>

                <div class="lightbox" aria-hidden="true">
                    <button class="lightbox__close" type="button" aria-label="Close image">
                        <i class="fas fa-times"></i>
                    </button>
                    <img class="lightbox__image" src="" alt="">
                </div>
            </div>

            <!-- 2. INFO SIDEBAR -->
            <div class="product-info-sidebar">
                <h1 class="product-titles"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>
                
                <div class="product-rating">
                    <div class="stars">
                        <?php 
                        $rating = (float)$product['rating'];
                        for ($i = 1; $i <= 5; $i++): 
                            if ($i <= $rating): ?>
                                <i class="fas fa-star"></i>
                            <?php elseif ($i - 0.5 <= $rating): ?>
                                <i class="fas fa-star-half-alt"></i>
                            <?php else: ?>
                                <i class="far fa-star"></i>
                            <?php endif; 
                        endfor; ?>
                    </div>
                    <span class="rating-text"><?= number_format($rating, 1) ?>/5 (<?= intval($product['review_count']) ?> Reviews)</span> |
                    <!-- CHECK REAL STOCK COLUMN -->
                    <strong class="<?= $product['stock'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                        <?= $product['stock'] > 0 ? $product['stock'] . ' In Stock' : 'Out of Stock' ?>
                    </strong>
                </div>

                <div class="product-price">
                    <span class="current">$<?= number_format($product['price'], 2) ?></span>
                    <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                        <span class="original">$<?= number_format($product['original_price'], 2) ?></span>
                        <span class="discount">Save $<?= number_format($product['original_price'] - $product['price'], 2) ?></span>
                    <?php endif; ?>
                </div>

                <div class="product-meta">
                    <div class="meta-item">
                        <i class="fas fa-tag"></i>
                        <span>Category: <strong><?= htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8') ?></strong></span>
                        <span>Sub Category: <strong><?= htmlspecialchars($product['sub_category_name'], ENT_QUOTES, 'UTF-8') ?></strong></span>
                    </div>

                    <!-- DYNAMIC VARIANTS: COLORS -->
                    <?php if(!empty($product['color'])): ?>
                        <div class="variant-option">
                            <h3>Available Colors:</h3>
                            <div class="color-options">
                                <?php foreach(explode(',', $product['color']) as $c): ?>
                                    <button type="button" class="color-option" data-type="color" data-value="<?= trim($c) ?>">
                                        <?= trim($c) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- DYNAMIC VARIANTS: STORAGE -->
                    <?php if(!empty($product['storage'])): ?>
                        <div class="variant-option">
                            <h3>Storage:</h3>
                            <div class="storage-options">
                                <?php foreach(explode(',', $product['storage']) as $s): ?>
                                    <button type="button" class="storage-option" data-type="storage" data-value="<?= trim($s) ?>">
                                        <?= trim($s) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="product-actions">
                        <div class="quantity-selector">
                            <button class="qty-btn minus">-</button>
                            <input type="number" value="1" min="1" max="<?= $product['stock'] ?>">
                            <button class="qty-btn plus">+</button>
                        </div>
                        <div class="btn-group">
                            <button id="add-to-cart-btn" class="btn btn-secondary btn-lg" data-id="<?= $product['id'] ?>">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                            <button id="buy-now-btn" class="btn btn-primary btn-lg" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                                <i class="fas fa-shopping-bag"></i> Buy Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. TABS SECTION -->
        <div class="product-tabs">
            <input type="radio" name="product-tabs" id="tab-desc" class="tab-input" checked>
            <input type="radio" name="product-tabs" id="tab-specs" class="tab-input">
            
            <div class="tab-labels">
                <label for="tab-desc" class="tab-label">Description</label>
                <label for="tab-specs" class="tab-label">Specifications</label>
            </div>
            
            <div class="tab-contents">
                <div class="tab-content" id="content-desc">
                    <h3>Product Overview</h3>
                    <p><?= nl2br(htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
                </div>
                
                <div class="tab-content" id="content-specs">
                    <h3>Technical Specifications</h3>
                    <?php if(!empty($product['specifications'])): ?>
                    <table class="specs-table">
                        <?php
                            // Split by the PIPE |
                            $pairs = explode('|', $product['specifications']);
                            foreach ($pairs as $pair):
                                $parts = explode(':', $pair, 2);
                                if (count($parts) < 2) continue;
                        ?>
                            <tr>
                                <td class="spec-label"><?= htmlspecialchars(trim($parts[0])) ?></td>
                                <td class="spec-value"><?= htmlspecialchars(trim($parts[1])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php else: ?>
                        <p>No specifications available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 4. RELATED PRODUCTS -->
        <section class="related-products">
            <h2>Related Products</h2>
            <div class="related-products-grid">
                <?php if (!empty($related)): ?>
                    <?php foreach ($related as $p): ?>
                        <!-- Point to your existing product card partial -->
                        <?php include __DIR__ . '/../admin/partials/product_card.view.php'; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No related products found.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>
<script src="assets/js/pages/product-detail.js"></script>