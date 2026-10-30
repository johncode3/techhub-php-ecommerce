<?php require_once '../resources/views/layouts/header.view.php'; ?>

<main class="cart">
    <div class="cart-page">
        <h1 class="cart-title">Your Cart</h1>

        <?php if (empty($_SESSION['cart'])): ?>
            <!-- IF BAG IS EMPTY -->
            <div style="text-align:center; padding: 50px;">
                <i class="fas fa-shopping-basket fa-4x" style="color:#ccc; margin-bottom:20px;"></i>
                <h2>Your cart is currently empty.</h2>
                <a href="index.php?page=product" class="return-btn" style="display:inline-block; margin-top:20px;">Go Shopping</a>
            </div>
        <?php else: ?>
            <!-- IF BAG HAS ITEMS -->
            <div class="cart-table">
                <div class="table-header">
                    <div class="col-product">Product</div>
                    <div class="col-price">Price</div>
                    <div class="col-quantity">Quantity</div>
                    <div class="col-subtotal">Subtotal</div>
                </div>

                <?php 
                $grandTotal = 0;
                foreach ($_SESSION['cart'] as $key => $item): 
                    $lineTotal = $item['price'] * $item['qty'];
                    $grandTotal += $lineTotal;
                ?>
                <div class="table-row">
                     <div class="col-product">
                        <div class="product-info">
                            <!-- Using the real image path from the session -->
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            <div class="product-detail">
                                <h3><?= htmlspecialchars($item['name']) ?></h3>
                                <div class="product-variant">
                                    <span>Color: <?= htmlspecialchars($item['color']) ?></span>
                                    <span>Storage: <?= htmlspecialchars($item['storage']) ?></span>
                                </div>
                            </div>
                        </div>
                     </div>
                    <div class="col-price">
                        <span class="price">$<?= number_format($item['price'], 2) ?></span>
                    </div>
                    <div class="col-quantity">
                        <div class="quantity-controls" data-key="<?= $key ?>">
                            <!-- We will link these to JS later tonight -->
                            <button class="qty-btn minus">-</button>
                            <input type="number" value="<?= $item['qty'] ?>" min="1" readonly>
                            <button class="qty-btn plus">+</button>
                        </div>
                    </div>
                    <div class="col-subtotal">
                        <span class="subtotal">$<?= number_format($lineTotal, 2) ?></span>
                        <!-- REMOVE BUTTON (Linking to the key we created) -->
                        <a href="index.php?page=cart-remove&key=<?= $key ?>" class="remove-btn" title="Remove item">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-actions">
                <a href="index.php?page=product" class="return-btn">
                    <i class="fas fa-arrow-left"></i> Return to Shop
                </a>
            </div>

            <div class="cart-totals-containter">
                <div class="cart-bottom-section">
                    <div class="coupon-box">
                        <h3>Have a coupon?</h3>
                        <p>Enter your code for a discount</p>
                        <div class="coupon-input-group">
                            <input type="text" placeholder="Coupon code">
                            <button class="apply-coupon-btn">Apply</button>
                        </div>
                    </div>
                    
                    <div class="cart-totals-box">
                        <h3>Cart Totals</h3>
                        <div class="totals-row">
                            <span>Subtotal</span>
                            <span class="amount">$ <?= number_format($grandTotal, 2) ?></span>
                        </div>
                        <div class="totals-row">
                            <span>Shipping</span>
                            <span class="shipping-option">Free Shipping</span>
                        </div>
                        <div class="totals-row grand-total">
                            <span>Total</span>
                            <span class="total-amount">$ <?= number_format($grandTotal, 2) ?></span>
                        </div>
                        <a href="index.php?page=checkout" class="checkout-btn">
                            <i class="fas fa-lock"></i> Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../resources/views/layouts/footer.view.php'; ?>
<script src="assets/js/pages/cart.js"></script>