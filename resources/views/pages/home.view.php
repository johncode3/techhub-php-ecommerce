<?php 
/** @var array $featured_products */ 
$featuredProducts = !empty($featured_products) ? $featured_products : []; 
?>

<main class="main-content">
    <div class="hero">
        <div class="container">
            <h2>Welcome to TechHub</h2>
            <p>Experience the future of technology with our latest arrivals.</p>
            <a href="index.php?page=product" class="btn btn-primary">Shop All Products</a>
        </div>
    </div>

    <section id="featured-products" class="product-section">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 50px;">
				<h2 class="section-title" style="margin-bottom: 15px; font-size: 2.5rem;">New & Featured</h2>

				<a href="index.php?page=product" class="btn btn-primary">
					View All Products <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
				</a>
			</div>
            
            <div class="product-grid">
                <?php if (!empty($featuredProducts)): ?>  
                    <?php foreach ($featuredProducts as $p): ?>
                    <?php include __DIR__ . '/../admin/partials/product_card.view.php'; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; font-size: 1.2rem; color: #777;">No featured products available at the moment. Please check back later!</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <div class="feature">
                <i class="fas fa-shipping-fast"></i>
                <h3>Free Shipping</h3>
                <p>On orders over $100</p>
            </div>
            <div class="feature">
                <i class="fas fa-undo-alt"></i>
                <h3>Easy Returns</h3>
                <p>30-day return policy</p>
            </div>
            <div class="feature">
                <i class="fas fa-lock"></i>
                <h3>Secure Payment</h3>
                <p>100% secure transactions</p>
            </div>
            <div class="feature">
                <i class="fas fa-headset"></i>
                <h3>24/7 Support</h3>
                <p>Dedicated support team</p>
            </div>
        </div>
    </section>
</main>