<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $reportRows */
/** @var array $summary */
/** @var array $errors */
/** @var array $stats */
$reportRows = $reportRows ?? [];
$summary = $summary ?? ['order_count' => 0, 'total_revenue' => 0];
$errors = $errors ?? [];
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
?>
<main class="admin-main container">
    <section style="display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;margin-bottom:24px;">
        <div>
            <h1 style="margin-bottom:8px;">Sales Report</h1>
            <p style="margin:0;color:#666;max-width:700px;">Date-wise summary of orders. Use the filters and print the report.</p>
        </div>
    </section>

    <form method="GET" action="index.php" class="product-filters filter-bar">
        <input type="hidden" name="page" value="admin-reports">
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <label>
                Start Date
                <input type="date" name="start_date" class="filter-input" value="<?= htmlspecialchars($startDate) ?>">
            </label>
            <label>
                End Date
                <input type="date" name="end_date" class="filter-input" value="<?= htmlspecialchars($endDate) ?>">
            </label>
            <button type="submit" class="btn btn-primary">Apply</button>
            <a href="index.php?page=admin-reports" class="btn btn-delete">Reset</a>
            <button type="button" class="btn btn-secondary" onclick="window.print()">Print</button>
        </div>
    </form>

    <?php if (!empty($errors)): ?>
        <div class="flash flash-error" style="margin-bottom:12px;">
            <?= htmlspecialchars($errors[0]) ?>
        </div>
    <?php endif; ?>

    <section class="stats-grid">
        <div class="card">
            <i class="fas fa-receipt"></i>
            <h3>Total Orders</h3>
            <p class="stat-number"><?= (int)$summary['order_count'] ?></p>
        </div>
        <div class="card">
            <i class="fas fa-dollar-sign"></i>
            <h3>Total Revenue</h3>
            <p class="stat-number">$<?= number_format((float)$summary['total_revenue'], 2) ?></p>
        </div>
    </section>

    <div class="table-responsive admin-table-responsive">
        <table class="admin-table reports-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reportRows)): ?>
                    <tr>
                        <td colspan="3" style="text-align:center;padding:40px;color:#888;">
                            No sales in the selected range.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reportRows as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['order_date'] ?? '') ?></td>
                            <td><?= (int)($row['order_count'] ?? 0) ?></td>
                            <td>$<?= number_format((float)($row['total_revenue'] ?? 0), 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>
