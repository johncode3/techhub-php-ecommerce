<?php
// Partial: related-products.view.php
// Expects: $related = array of products (each with id,name,main_image,category,price,original_price)
// If $related is empty, render a built-in static fallback grid.
/** @var array $related */
$related = $related ?? [];
?>
<section class="related-products">
    <h2>Related Products</h2>
    <div class="product-grid">
        <?php if (empty($related) || !is_array($related)): ?>
            <?php
            // Fallback set of sample products for layout consistency.
            $fallback = [
                ['id'=>1,'name'=>'iPhone 17 Pro Max','main_image'=>'assets/images/product_images/phone/iphone17proMax.webp','category'=>'Phone','price'=>1680,'original_price'=>1999,'stock'=>10,'is_new'=>1],
                ['id'=>2,'name'=>'iPhone 17 Pro Max','main_image'=>'assets/images/product_images/phone/iPhone 17 pro max/all color.png','category'=>'Phone','price'=>1680,'original_price'=>1999,'stock'=>5,'is_new'=>0],
                ['id'=>3,'name'=>'Samsung Galaxy S24 Ultra','main_image'=>'assets/images/product_images/phone/samsung_galaxy_s24_ultra.jpg','category'=>'Phone','price'=>1299,'original_price'=>1499,'stock'=>8,'is_new'=>0],
                ['id'=>4,'name'=>'MacBook Pro 16" M4','main_image'=>'assets/images/product_images/computer/apple-m4-macbook-pro.avif','category'=>'Computer','price'=>2499,'original_price'=>0,'stock'=>3,'is_new'=>1],
            ];
            foreach ($fallback as $p):
                // render card partial
                include __DIR__ . '/product_card.view.php';
            endforeach;
            ?>
        <?php else: ?>
            <?php foreach ($related as $rp):
                $p = $rp; // product_card expects $p
                include __DIR__ . '/product_card.view.php';
            endforeach; ?>
        <?php endif; ?>
    </div>
</section>
