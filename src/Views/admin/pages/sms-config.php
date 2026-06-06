<?php
/** @var array $config */
/** @var int|null $credit */
/** @var string|null $error */
$title = 'مدیریت پیامک';
$isConfigured = ($config['api_key'] ?? '') !== '';
ob_start();
?>

<div class="admin-page-header">
    <h2><i class="bi bi-chat-dots-fill me-2"></i>مدیریت پیامک (IPPanel)</h2>
    <p>تنظیمات ارسال پیامک، پترن‌ها و ارسال تستی</p>
</div>

<?php if ($flash = $_SESSION['flash'] ?? null): ?>
    <?php unset($_SESSION['flash']); ?>
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert" style="border-radius:var(--radius-sm);font-size:.85rem">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Status & Credit -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon <?= $isConfigured ? 'green' : 'orange' ?>">
                    <i class="bi bi-<?= $isConfigured ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
                </div>
                <div>
                    <div class="stat-label">وضعیت اتصال</div>
                    <div class="stat-value" style="font-size:1rem"><?= $isConfigured ? 'متصل' : 'تنظیم نشده' ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon blue"><i class="bi bi-wallet2"></i></div>
                <div>
                    <div class="stat-label">اعتبار حساب</div>
                    <div class="stat-value" style="font-size:1rem"><?= $credit !== null ? number_format($credit) . ' ریال' : '—' ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon purple"><i class="bi bi-telephone-fill"></i></div>
                <div>
                    <div class="stat-label">شماره ارسال</div>
                    <div class="stat-value" style="font-size:1rem" dir="ltr"><?= e($config['from_number'] ?: '—') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Configuration Panel -->
    <div class="col-lg-6">
        <div class="form-section">
            <div class="form-section-title"><i class="bi bi-gear-fill"></i>تنظیمات سرویس</div>

            <div class="config-group mb-3">
                <label class="form-label">کلید API</label>
                <input type="text" class="form-control" value="<?= $isConfigured ? substr($config['api_key'], 0, 8) . '••••••••' : '' ?>" disabled>
                <small class="text-muted mt-1 d-block">در فایل <code>.env</code> با کلید <code>IPPANEL_API_KEY</code> تنظیم کنید</small>
            </div>

            <div class="config-group mb-3">
                <label class="form-label">آدرس API</label>
                <input type="text" class="form-control" value="<?= e($config['base_url']) ?>" disabled dir="ltr">
            </div>

            <div class="config-group mb-3">
                <label class="form-label">شماره ارسال‌کننده</label>
                <input type="text" class="form-control" value="<?= e($config['from_number']) ?>" disabled dir="ltr">
                <small class="text-muted mt-1 d-block">تنظیم: <code>IPPANEL_FROM_NUMBER</code></small>
            </div>

            <div class="config-group mb-3">
                <label class="form-label">روش ارسال OTP</label>
                <input type="text" class="form-control" value="<?= e($config['otp_method']) ?>" disabled>
                <small class="text-muted mt-1 d-block">مقادیر مجاز: <code>pattern</code> / <code>votp</code></small>
            </div>

            <div class="config-group mb-3">
                <label class="form-label">پارامتر OTP</label>
                <input type="text" class="form-control" value="<?= e($config['otp_param']) ?>" disabled>
            </div>
        </div>
    </div>

    <!-- Patterns Panel -->
    <div class="col-lg-6">
        <div class="form-section">
            <div class="form-section-title"><i class="bi bi-braces"></i>پترن‌های پیامکی</div>

            <div class="config-group mb-3">
                <label class="form-label">پترن OTP</label>
                <input type="text" class="form-control" value="<?= e($config['pattern_otp'] ?: 'تنظیم نشده') ?>" disabled dir="ltr">
                <small class="text-muted mt-1 d-block"><code>IPPANEL_PATTERN_OTP</code></small>
            </div>

            <div class="config-group mb-3">
                <label class="form-label">پترن خوشامدگویی</label>
                <input type="text" class="form-control" value="<?= e($config['pattern_welcome'] ?: 'تنظیم نشده') ?>" disabled dir="ltr">
                <small class="text-muted mt-1 d-block"><code>IPPANEL_PATTERN_WELCOME</code></small>
            </div>

            <div class="config-group mb-3">
                <label class="form-label">پترن بستن معامله</label>
                <input type="text" class="form-control" value="<?= e($config['pattern_deal_closed'] ?: 'تنظیم نشده') ?>" disabled dir="ltr">
                <small class="text-muted mt-1 d-block"><code>IPPANEL_PATTERN_DEAL_CLOSED</code></small>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-warning" style="font-size:.8rem;border-radius:var(--radius-sm)">
                    <i class="bi bi-exclamation-triangle me-1"></i>خطا در اتصال: <?= e($error) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Send Test SMS -->
<div class="row g-3 mt-2">
    <div class="col-lg-6">
        <div class="form-section">
            <div class="form-section-title"><i class="bi bi-send-fill"></i>ارسال پیامک تستی</div>
            <form method="POST" action="<?= url('/admin/sms/send-test') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem">شماره موبایل</label>
                    <input type="text" name="phone" class="form-control" placeholder="09120000000" dir="ltr" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem">متن پیام</label>
                    <textarea name="message" class="form-control" rows="3" placeholder="متن پیامک تستی..." required></textarea>
                </div>
                <button type="submit" class="btn btn-admin-primary" <?= !$isConfigured ? 'disabled' : '' ?>>
                    <i class="bi bi-send me-1"></i>ارسال
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-section">
            <div class="form-section-title"><i class="bi bi-code-square"></i>ارسال با پترن</div>
            <form method="POST" action="<?= url('/admin/sms/send-pattern') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem">شماره موبایل</label>
                    <input type="text" name="phone" class="form-control" placeholder="09120000000" dir="ltr" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem">کد پترن</label>
                    <input type="text" name="pattern_code" class="form-control" placeholder="xxxxxxxxxxx" dir="ltr" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem">پارامترها (JSON)</label>
                    <textarea name="params" class="form-control" rows="2" dir="ltr" placeholder='{"code": "12345"}'>{}</textarea>
                </div>
                <button type="submit" class="btn btn-admin-primary" <?= !$isConfigured ? 'disabled' : '' ?>>
                    <i class="bi bi-braces me-1"></i>ارسال پترن
                </button>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
