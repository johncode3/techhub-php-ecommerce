<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $order */
/** @var array $orderItems */
/** @var array $errors */
$order = $order ?? null;
$orderItems = $orderItems ?? [];
$errors = $errors ?? [];
?>
<main>
    <h1>Order Details</h1>

    <?php if (!empty($errors)): ?>
        <div class="flash flash-error" style="margin-bottom:12px;">
            <?= htmlspecialchars($errors[0]) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($order)): ?>
        <p style="color:#888;">Order not found.</p>
    <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;margin-bottom:18px;">
            <div class="card" style="padding:16px;border:1px solid #eee;border-radius:10px;">
                <h3 style="margin:0 0 8px;">Customer</h3>
                <p><strong><?= htmlspecialchars($order['full_name'] ?? 'Guest') ?></strong></p>
                <p><?= htmlspecialchars($order['email'] ?? '') ?></p>
                <p><?= htmlspecialchars($order['phone'] ?? '') ?></p>
                <p><?= htmlspecialchars($order['address'] ?? '') ?></p>
            </div>
            <div class="card" style="padding:16px;border:1px solid #eee;border-radius:10px;">
                <h3 style="margin:0 0 8px;">Order Info</h3>
                <p>Order ID: <strong>#<?= (int)$order['id'] ?></strong></p>
                <p>Date: <?= !empty($order['created_at']) ? date('M d, Y', strtotime($order['created_at'])) : '-' ?></p>
                <p>Payment: <?= htmlspecialchars($order['payment_method'] ?? '') ?></p>
                <p>Total: $<?= number_format((float)($order['total_amount'] ?? 0), 2) ?></p>
            </div>
            <div class="card" style="padding:16px;border:1px solid #eee;border-radius:10px;">
                <h3 style="margin:0 0 8px;">Status</h3>
                <form method="POST" action="index.php?page=admin-order-show&id=<?= (int)$order['id'] ?>">
                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                    <select name="order_status" class="filter-input" style="width:100%;margin-bottom:10px;">
                        <?php
                            $currentStatus = $order['order_status'] ?? 'Pending';
                            $statuses = ['Pending', 'Paid', 'Shipped', 'Completed', 'Cancelled'];
                        ?>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status ?>" <?= ($currentStatus === $status) ? 'selected' : '' ?>>
                                <?= $status ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Update Status</button>
                </form>
            </div>
        </div>

        <div class="table-responsive admin-table-responsive">
            <table class="admin-table orders-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Variant</th>
                        <th>Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orderItems)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center;padding:40px;color:#888;">
                                No items found for this order.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orderItems as $item): ?>
                            <?php $lineTotal = (float)$item['price'] * (int)$item['qty']; ?>
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <?php $img = $item['main_image'] ?? ''; ?>
                                        <img src="<?= htmlspecialchars($img !== '' ? $img : 'https://via.placeholder.com/48') ?>" alt="<?= htmlspecialchars($item['product_name'] ?? '') ?>" style="width:48px;height:48px;object-fit:cover;border-radius:8px;">
                                        <div style="font-weight:600;"><?= htmlspecialchars($item['product_name'] ?? '') ?></div>
                                    </div>
                                </td>
                                <td>$<?= number_format((float)($item['price'] ?? 0), 2) ?></td>
                                <td><?= (int)$item['qty'] ?></td>
                                <td><?= htmlspecialchars($item['variant_info'] ?? '') ?></td>
                                <td>$<?= number_format($lineTotal, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>
