<?php
$title = 'تنظیمات';
ob_start();
?>
<div class="admin-page-header">
    <h2><i class="bi bi-gear me-2"></i>تنظیمات</h2>
    <p>تنظیمات سیستم و اتصالات</p>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="settings-card">
            <h6><i class="bi bi-cloud me-2 text-primary"></i>اتصال HubSpot</h6>
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small">وضعیت اتصال:</span>
                <?php if (!empty($hubspot_connected)): ?>
                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>متصل</span>
                <?php else: ?>
                    <span class="badge bg-danger-subtle text-danger"><i class="bi bi-x-circle me-1"></i>قطع</span>
                <?php endif; ?>
            </div>
            <hr>
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small">توکن:</span>
                <code class="small"><?= e($hubspot_token_masked ?? '••••••••') ?></code>
            </div>
        </div>

        <div class="settings-card">
            <h6><i class="bi bi-phone me-2 text-success"></i>سرویس پیامک (IPPanel)</h6>
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small">وضعیت:</span>
                <?php if (!empty($sms_configured)): ?>
                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>فعال</span>
                <?php else: ?>
                    <span class="badge bg-warning-subtle text-warning"><i class="bi bi-exclamation-circle me-1"></i>تنظیم نشده</span>
                <?php endif; ?>
            </div>
            <hr>
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small">شماره ارسال:</span>
                <span class="small fw-semibold"><?= e(config('ippanel.from_number') ?: '—') ?></span>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="settings-card">
            <h6><i class="bi bi-shield-check me-2 text-info"></i>سرویس زحل (استعلام هویت)</h6>
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small">وضعیت:</span>
                <?php if (!empty($zohal_configured)): ?>
                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>فعال</span>
                <?php else: ?>
                    <span class="badge bg-warning-subtle text-warning"><i class="bi bi-exclamation-circle me-1"></i>تنظیم نشده</span>
                <?php endif; ?>
            </div>
            <hr>
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small">آدرس API:</span>
                <span class="small fw-semibold"><?= e(config('zohal.base_url') ?: '—') ?></span>
            </div>
        </div>

        <div class="settings-card">
            <h6><i class="bi bi-person-lock me-2 text-warning"></i>حساب‌های مدیر</h6>
            <?php foreach (config('admin_users') as $email => $pass): ?>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small"><?= e($email) ?></span>
                    <span class="badge bg-light text-dark"><i class="bi bi-key me-1"></i>••••</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
