<?php
/** @var array $user */
/** @var array $orders */
/** @var array $selectedOrder */
/** @var array $orderItems */
/** @var array $errors */
?>
<?php
$totalOrders = count($orders);
$totalSpent = 0.0;
$lastOrderDate = null;
if (!empty($orders)) {
    foreach ($orders as $order) {
        $totalSpent += (float)($order['total_amount'] ?? 0);
    }
    $lastOrderDate = $orders[0]['created_at'] ?? null;
}

function accountStatusClass(string $status): string
{
    $slug = strtolower(trim($status));
    $slug = str_replace(' ', '-', $slug);
    $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
    return $slug !== '' ? $slug : 'unknown';
}
?>

<div class="account-page">
    <div class="container">
        <div class="account-hero">
            <div class="account-hero__content">
                <span class="account-kicker">Account Overview</span>
                <h1>Welcome back<?= !empty($user['username']) ? ', ' . htmlspecialchars($user['username']) : '' ?></h1>
                <p>Track your purchases, review order details, and keep your profile in sync.</p>
                <div class="account-hero__meta">
                    <span><?= htmlspecialchars($user['email'] ?? 'No email on file') ?></span>
                    <?php if (!empty($user['created_at'])): ?>
                        <span>Member since <?= htmlspecialchars(date('M Y', strtotime($user['created_at']))) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="account-hero__card">
                <h2>Quick Stats</h2>
                <div class="account-summary">
                    <div class="account-summary__item">
                        <span>Total Orders</span>
                        <strong><?= (int)$totalOrders ?></strong>
                    </div>
                    <div class="account-summary__item">
                        <span>Total Spent</span>
                        <strong>$<?= number_format($totalSpent, 2) ?></strong>
                    </div>
                    <div class="account-summary__item">
                        <span>Last Order</span>
                        <strong><?= $lastOrderDate ? htmlspecialchars(date('M d, Y', strtotime($lastOrderDate))) : 'None yet' ?></strong>
                    </div>
                </div>
                <a class="account-action" href="index.php?page=product">Continue shopping</a>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="account-alert">Something went wrong loading your account.</div>
        <?php endif; ?>

        <div class="account-grid">
            <section class="account-card">
                <h2>Profile Snapshot</h2>
                <?php if (!empty($user)): ?>
                    <div class="account-info">
                        <div>
                            <span>Username</span>
                            <strong><?= htmlspecialchars($user['username'] ?? '') ?></strong>
                        </div>
                        <div>
                            <span>Email</span>
                            <strong><?= htmlspecialchars($user['email'] ?? '') ?></strong>
                        </div>
                        <div>
                            <span>Member Since</span>
                            <strong><?= htmlspecialchars(date('M d, Y', strtotime($user['created_at'] ?? 'now'))) ?></strong>
                        </div>
                    </div>
                <?php else: ?>
                    <p>No profile details found.</p>
                <?php endif; ?>
            </section>

            <section class="account-card">
                <h2>Order History</h2>
                <?php if (!empty($orders)): ?>
                    <div class="account-table-wrap">
                        <table class="account-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <?php $status = (string)($order['order_status'] ?? ''); ?>
                                    <tr>
                                        <td>#<?= (int)$order['id'] ?></td>
                                        <td><?= htmlspecialchars(date('M d, Y', strtotime($order['created_at'] ?? 'now'))) ?></td>
                                        <td>
                                            <span class="account-status account-status--<?= accountStatusClass($status) ?>">
                                                <?= htmlspecialchars($status) ?>
                                            </span>
                                        </td>
                                        <td>$<?= number_format((float)($order['total_amount'] ?? 0), 2) ?></td>
                                        <td>
                                            <a class="account-link" href="index.php?page=account&order_id=<?= (int)$order['id'] ?>">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>You have no orders yet.</p>
                <?php endif; ?>
            </section>
        </div>

        <section class="account-card account-detail">
            <h2>Order Details</h2>
            <?php if (!empty($selectedOrder)): ?>
                <div class="account-info account-info--compact">
                    <div>
                        <span>Order</span>
                        <strong>#<?= (int)$selectedOrder['id'] ?></strong>
                    </div>
                    <div>
                        <span>Status</span>
                        <strong><?= htmlspecialchars($selectedOrder['order_status'] ?? '') ?></strong>
                    </div>
                    <div>
                        <span>Placed</span>
                        <strong><?= htmlspecialchars(date('M d, Y', strtotime($selectedOrder['created_at'] ?? 'now'))) ?></strong>
                    </div>
                    <div>
                        <span>Payment</span>
                        <strong><?= htmlspecialchars($selectedOrder['payment_method'] ?? '') ?></strong>
                    </div>
                </div>

                <div class="account-table-wrap">
                    <table class="account-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Variant</th>
                                <th>Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderItems as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($item['variant_info'] ?? '-') ?></td>
                                    <td><?= (int)($item['qty'] ?? 0) ?></td>
                                    <td>$<?= number_format((float)($item['price'] ?? 0), 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Select an order from the list to see details.</p>
            <?php endif; ?>
        </section>
    </div>
</div>
