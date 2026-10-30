<?php require_once '../resources/views/admin/layouts/header.view.php'; ?>
<?php
/** @var array $adminUser */
/** @var array $errors */
?>

<main class="admin-main container">
    <section style="display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;margin-bottom:24px;">
        <div>
            <h1 style="margin-bottom:8px;">My Account</h1>
            <p style="margin:0;color:#666;max-width:700px;">Manage your admin profile and password.</p>
        </div>
    </section>

    <?php if (!empty($errors)): ?>
        <div class="flash flash-error" style="margin-bottom:12px;">
            <?= htmlspecialchars($errors[0]) ?>
        </div>
    <?php endif; ?>

    <section style="display:grid;grid-template-columns:1fr 1fr;gap:22px;align-items:start;">
        <div class="admin-form" style="margin:0;">
            <h2 style="margin-top:0;">Profile</h2>
            <?php if (!empty($adminUser)): ?>
                <div style="display:grid;gap:12px;">
                    <div>
                        <div style="font-size:0.85rem;color:#777;">Username</div>
                        <div style="font-weight:700;"><?= htmlspecialchars($adminUser['username'] ?? '') ?></div>
                    </div>
                    <div>
                        <div style="font-size:0.85rem;color:#777;">Email</div>
                        <div style="font-weight:700;"><?= htmlspecialchars($adminUser['email'] ?? '') ?></div>
                    </div>
                    <div>
                        <div style="font-size:0.85rem;color:#777;">Role</div>
                        <div style="font-weight:700;"><?= htmlspecialchars($adminUser['role'] ?? 'admin') ?></div>
                    </div>
                    <div>
                        <div style="font-size:0.85rem;color:#777;">Member Since</div>
                        <div style="font-weight:700;">
                            <?= htmlspecialchars(date('M d, Y', strtotime($adminUser['created_at'] ?? 'now'))) ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p>No admin profile data found.</p>
            <?php endif; ?>
        </div>

        <div class="admin-form" style="margin:0;">
            <h2 style="margin-top:0;">Change Password</h2>
            <form method="POST" action="index.php?page=admin-my-account">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-edit">Update Password</button>
            </form>
        </div>
    </section>
</main>

<?php require_once '../resources/views/admin/layouts/footer.view.php'; ?>
