<?php 
loginRequire(); 
require_once '../resources/views/layouts/header.view.php'; 
?>

<main class="checkout">
    <div class="checkout-page">
        <!-- WRAP EVERYTHING IN ONE FORM -->
        <form id="order-form" class="checkout-form-wrapper" style="display:contents;">
            <div class="checkout-left">
                <div class="checkout-step">
                    <h2><i class="fas fa-user"></i> Billing Details</h2>
                    <div class="bill-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label>First Name *</label>
                                <input type="text" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name *</label>
                                <input type="text" name="last_name" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Street Address *</label>
                            <input type="text" name="address" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Town/City *</label>
                                <input type="text" name="city" required>
                            </div>
                            <div class="form-group">
                                <label>ZIP Code *</label>
                                <input type="text" name="zip" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Phone *</label>
                            <input type="tel" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="email" required>
                        </div>
                    </div>
                </div>

                <div class="checkout-step">
                    <h2><i class="fas fa-shipping-fast"></i> Shipping Method</h2>
                    <div class="shipping-options">
                        <div class="shipping-option">
                            <input type="radio" name="shipping" id="free-shipping" value="0" checked>
                            <label for="free-shipping">
                                <span class="shipping-name">Free Shipping</span>
                                <span class="shipping-price">FREE</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="checkout-right">
                <div class="order-summary">
                    <h2><i class="fas fa-receipt"></i> Order Summary</h2>
                    
                    <div class="order-items">
                        <?php 
                        $subtotal = 0;
                        foreach ($_SESSION['cart'] as $item): 
                            $subtotal += ($item['price'] * $item['qty']);
                        ?>
                        <div class="order-item">
                            <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
                            <div class="item-details">
                                <h4><?= htmlspecialchars($item['name']) ?></h4>
                                <span class="item-variant"><?= $item['color'] ?> • <?= $item['storage'] ?> (x<?= $item['qty'] ?>)</span>
                            </div>
                            <div class="item-price">$<?= number_format($item['price'] * $item['qty'], 2) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-totals">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span>$<?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="total-row grand-total">
                            <span>Total</span>
                            <span class="total-price">$<?= number_format($subtotal, 2) ?></span>
                        </div>
                    </div>

                    <div class="payment-method">
                        <h4><i class="fas fa-credit-card"></i> Payment Method</h4>
                        <div class="payment-options">
                            <div class="payment-option">
                                <input type="radio" name="payment" value="Bank Transfer" id="bank-transfer" checked>
                                <label for="bank-transfer"><i class="fas fa-university"></i> Direct Bank Transfer</label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" name="payment" value="PayPal" id="paypal">
                                <label for="paypal"><i class="fab fa-paypal"></i> PayPal</label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" name="payment" value="COD" id="cod">
                                <label for="cod"><i class="fas fa-money-bill-wave"></i> Cash on Delivery</label>
                            </div>
                        </div>
                    </div>

                    <div id="paypal-button-container" style="margin-top: 20px; display: none;"></div>
                    <button type="submit" id="manual-place-order" class="place-order-btn" style="margin-top: 20px; width: 100%;">
                        <i class="fas fa-lock"></i> Place Order
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<script>
// 1. SELECT ELEMENTS
const orderForm = document.getElementById('order-form');
const paypalContainer = document.getElementById('paypal-button-container');
const manualBtn = document.getElementById('manual-place-order');
const paymentRadios = document.querySelectorAll('input[name="payment"]');

// 2. TOGGLE VISIBILITY BASED ON PAYMENT METHOD
function togglePaymentUI() {
    const selectedMethod = document.querySelector('input[name="payment"]:checked').value;
    
    if (selectedMethod === 'PayPal') {
        paypalContainer.style.display = 'block';
        manualBtn.style.display = 'none';
        
        // Dynamically load SDK if not loaded yet
        if (!document.getElementById('paypal-sdk-script')) {
            loadPayPalSDK();
        }
    } else {
        paypalContainer.style.display = 'none';
        manualBtn.style.display = 'block';
    }
}

// 3. DYNAMICALLY LOAD PAYPAL SDK
function loadPayPalSDK() {
    paypalContainer.innerHTML = 'Loading PayPal...';
    
    const script = document.createElement('script');
    script.id = 'paypal-sdk-script';
    // Use the client ID securely passed from PHP
    script.src = "https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID ?>&currency=USD";
    script.setAttribute("data-namespace", "paypal_sdk");
    script.onload = initPayPalButtons;
    script.onerror = function() {
        console.error('PayPal SDK failed to load.');
        paypalContainer.innerHTML = '<div style="padding:10px;border:1px solid #f3b4b4;color:#b52a2a;border-radius:8px;">PayPal failed to load. Please check your client ID or disable Tracking Prevention/Ad Blockers in Edge.</div>';
    };
    document.body.appendChild(script);
}

// 4. INIT PAYPAL BUTTONS AFTER LOAD
function initPayPalButtons() {
    paypalContainer.innerHTML = ''; // Clear loading text
    if (window.paypal_sdk && paypal_sdk.Buttons) {
        paypal_sdk.Buttons({
            onClick: function(data, actions) {
                // Ensure form is filled before letting PayPal open
                if (!orderForm.checkValidity()) {
                    alert("Please fill out all billing and shipping information before you can place an order.");
                    orderForm.reportValidity(); // This visually highlights empty required fields
                    return actions.reject();    // Abort the PayPal click
                }
                return actions.resolve();       // Let PayPal open
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= number_format($subtotal, 2, '.', '') ?>'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    console.log("Transaction ID: " + details.id);
                    submitOrderToPHP(details.id);
                });
            }
        }).render('#paypal-button-container');
    } else {
        paypalContainer.innerHTML = '<div style="padding:10px;border:1px solid #f3b4b4;color:#b52a2a;border-radius:8px;">PayPal rendering failed.</div>';
    }
}

// Listen for clicks on the radio buttons
paymentRadios.forEach(radio => {
    radio.addEventListener('change', togglePaymentUI);
});

// Initialize on page load
togglePaymentUI();

// 5. HANDLE MANUAL SUBMISSION (COD / Bank Transfer)
orderForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Safety check: If PayPal is selected, the PayPal SDK handles the click, not this form listener
    const selectedMethod = document.querySelector('input[name="payment"]:checked').value;
    if (selectedMethod === 'PayPal') return;

    manualBtn.disabled = true;
    manualBtn.innerText = "Processing Order...";

    await submitOrderToPHP('MANUAL');
});

// 6. SHARED FUNCTION TO TALK TO PHP
async function submitOrderToPHP(paymentId) {
    const formData = new FormData(orderForm);
    const selectedMethod = document.querySelector('input[name="payment"]:checked').value;
    
    formData.append('payment_method', selectedMethod);
    formData.append('payment_id', paymentId); // Will be 'MANUAL' or a PayPal ID

    try {
        const response = await fetch('index.php?page=place-order', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        if (result.success) {
            alert("Success! Your Order #" + result.order_id + " has been placed.");
            window.location.href = "index.php?page=home";
        } else {
            alert("Error: " + result.message);
            manualBtn.disabled = false;
            manualBtn.innerText = "Place Order";
        }
    } catch (error) {
        console.error("Order Error:", error);
        alert("Connection lost. Please check your internet.");
    }
}
</script>

<?php require_once '../resources/views/layouts/footer.view.php'; ?>