<?php
$title = 'انتخاب مالک';
ob_start();
?>
<div class="auth-container">
    <div class="auth-card auth-card-wide auth-form">
        <h1 class="auth-title">انتخاب مالک HubSpot</h1>
        <p class="auth-subtitle">برای فیلتر داده‌های مدیریتی، یک مالک انتخاب کنید</p>

        <form method="POST" action="<?= url('/admin/owner-select') ?>">
            <?= csrf_field() ?>
            <div class="list-group mb-4">
                <?php if ($owners): ?>
                    <?php foreach ($owners as $owner): ?>
                        <?php $ownerName = trim(($owner['firstName'] ?? '') . ' ' . ($owner['lastName'] ?? '')); ?>
                        <label class="list-group-item list-group-item-action owner-option d-flex gap-3 align-items-center">
                            <input type="radio" name="owner_id" value="<?= e($owner['id'] . '|' . $ownerName) ?>" required
                                   <?= (($selectedOwner['id'] ?? '') == ($owner['id'] ?? '')) ? 'checked' : '' ?>>
                            <div class="min-w-0">
                                <strong class="d-block text-break-word"><?= e($ownerName) ?></strong>
                                <small class="text-muted text-break-word"><?= e($owner['email'] ?? '') ?></small>
                            </div>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">مالکی یافت نشد. HUBSPOT_TOKEN را بررسی کنید.</div>
                    <input type="hidden" name="owner_id" value="default|Default">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary w-100 auth-btn">ادامه به داشبورد</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/app.php';
