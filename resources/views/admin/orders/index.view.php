<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $orders */
/** @var array $errors */
$orders = $orders ?? [];
$errors = $errors ?? [];
?>

<main>
    <h1>Order Management</h1>
    <p>View and manage customer orders.</p>

    <form method="GET" action="index.php" class="product-filters filter-bar">
        <input type="hidden" name="page" value="admin-orders">

        <div class="filter-search">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search order..." class="filter-input">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </div>

        <select name="status" class="filter-input" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="Pending" <?= (($_GET['status'] ?? '') === 'Pending') ? 'selected' : '' ?>>Pending</option>
            <option value="Paid" <?= (($_GET['status'] ?? '') === 'Paid') ? 'selected' : '' ?>>Paid</option>
            <option value="Shipped" <?= (($_GET['status'] ?? '') === 'Shipped') ? 'selected' : '' ?>>Shipped</option>
            <option value="Completed" <?= (($_GET['status'] ?? '') === 'Completed') ? 'selected' : '' ?>>Completed</option>
            <option value="Cancelled" <?= (($_GET['status'] ?? '') === 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
        </select>

        <a href="index.php?page=admin-orders" class="btn btn-delete">Reset</a>
    </form>

    <?php if (!empty($errors)): ?>
        <div class="flash flash-error" style="margin-bottom:12px;">
            <?= htmlspecialchars($errors[0]) ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive admin-table-responsive">
        <table class="admin-table orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:#888;">
                            No orders found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <?php
                            $status = $order['order_status'] ?? 'Pending';
                            $statusClass = 'status-inactive';
                            $statusStyle = '';
                            if (in_array($status, ['Paid', 'Completed'], true)) {
                                $statusClass = 'status-active';
                            } elseif ($status === 'Pending') {
                                $statusStyle = 'background:#fff8e1;color:#b26a00;border:1px solid #ffe0a3;';
                            } elseif ($status === 'Shipped') {
                                $statusStyle = 'background:#e8f4ff;color:#1e6fbf;border:1px solid #b7dcff;';
                            }
                        ?>
                        <tr>
                            <td>#<?= (int)$order['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($order['full_name'] ?? ($order['user_name'] ?? 'Guest')) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($order['email'] ?? '') ?></td>
                            <td>$<?= number_format((float)($order['total_amount'] ?? 0), 2) ?></td>
                            <td><?= htmlspecialchars($order['payment_method'] ?? '') ?></td>
                            <td>
                                <span class="status-pill <?= $statusClass ?>" style="<?= $statusStyle ?>">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                            </td>
                            <td><?= !empty($order['created_at']) ? date('M d, Y', strtotime($order['created_at'])) : '-' ?></td>
                            <td>
                                <a href="index.php?page=admin-order-show&id=<?= (int)$order['id'] ?>" class="btn btn-edit">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>