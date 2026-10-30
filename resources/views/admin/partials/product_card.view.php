<?php
/** @var array $p */

// 1. Data Preparation & Sanitization
$id = (int)($p['id'] ?? 0);
$name = htmlspecialchars($p['name'] ?? 'Unknown Product');
$image = htmlspecialchars($p['main_image'] ?? 'assets/images/placeholder.png');
$category = htmlspecialchars($p['category_name'] ?? 'Tech');

$price = (float)($p['price'] ?? 0);
$origPrice = (float)($p['original_price'] ?? 0);
$stock = (int)($p['stock'] ?? 0);
$rating = (float)($p['rating'] ?? 0);
$isNew = !empty($p['is_new']);
$isSale = ($origPrice > $price);
?>

<div class="product-card">
    <!-- 2. Badges -->
    <?php if ($stock <= 0): ?>
        <div class="product-badge out-of-stock">Out of Stock</div>
    <?php elseif ($isSale): ?>
        <?php $pct = round((($origPrice - $price) / $origPrice) * 100); ?>
        <div class="product-badge sale">SAVE <?= $pct ?>%</div>
    <?php elseif ($isNew): ?>
        <div class="product-badge new">NEW</div>
    <?php endif; ?>

    <!-- 3. Link & Content -->
    <a href="index.php?page=product_detail&id=<?= $id ?>" class="product-link">
        <div class="product-image">
            <img src="<?= $image ?>" alt="<?= $name ?>" loading="lazy">
        </div>
        
        <div class="product-detail">
            <span class="product-category"><?= $category ?></span>
            <h3 class="product-title"><?= $name ?></h3>
            
            <div class="product-rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if ($i <= $rating): ?>
                        <i class="fas fa-star"></i>
                    <?php elseif ($i - 0.5 <= $rating): ?>
                        <i class="fas fa-star-half-alt"></i>
                    <?php else: ?>
                        <i class="far fa-star"></i>
                    <?php endif; ?>
                <?php endfor; ?>
                <span class="review-count">(<?= (int)($p['review_count'] ?? 0) ?>)</span>
            </div>

            <div class="product-price">
                <span class="current-price">$<?= number_format($price, 2) ?></span>
                <?php if ($isSale): ?>
                    <span class="original-price">$<?= number_format($origPrice, 2) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </a>
    
    <!-- 4. Action -->
        <button class="btn btn-block add-to-cart" 
            data-id="<?= $id ?>" 
            <?= ($stock <= 0) ? 'disabled' : '' ?>>
        <i class="fas fa-shopping-cart"></i> 
        <?= ($stock <= 0) ? 'Sold Out' : 'Add to Cart' ?>
    </button>
</div>