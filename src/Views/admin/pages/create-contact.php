<?php
$title = 'ثبت مخاطب جدید';
$step = $registration['step'] ?? 1;
ob_start();
?>
<div class="admin-page-header">
    <h2><i class="bi bi-person-plus me-2"></i>ثبت مخاطب جدید</h2>
    <p>ایجاد مخاطب در HubSpot با استعلام هویت</p>
</div>

<!-- Step indicator -->
<div class="steps-indicator">
    <div class="step-item <?= $step >= 1 ? ($step > 1 ? 'done' : 'active') : '' ?>">
        <i class="bi bi-1-circle me-1"></i>احراز هویت
    </div>
    <div class="step-item <?= $step >= 2 ? ($step > 2 ? 'done' : 'active') : '' ?>">
        <i class="bi bi-2-circle me-1"></i>شماره موبایل
    </div>
    <div class="step-item <?= $step >= 3 ? ($step > 3 ? 'done' : 'active') : '' ?>">
        <i class="bi bi-3-circle me-1"></i>تأیید OTP
    </div>
    <div class="step-item <?= $step >= 4 ? 'active' : '' ?>">
        <i class="bi bi-4-circle me-1"></i>اطلاعات تکمیلی
    </div>
</div>

<?php $flash = flash_messages(); ?>
<?php if (!empty($flash['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i><?= e($flash['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i><?= e($flash['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">

        <?php if ($step === 1): ?>
        <!-- Step 1: Identity verification -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="bi bi-shield-check"></i>مرحله ۱: احراز هویت با کد ملی
            </div>
            <form method="POST" action="<?= url('/admin/contacts/create/verify-identity') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" for="national_code">کد ملی</label>
                        <input type="text" id="national_code" name="national_code"
                               class="form-control form-control-lg <?= has_error('national_code') ? 'is-invalid' : '' ?>"
                               value="<?= e(old('national_code', $registration['national_code'] ?? '')) ?>"
                               maxlength="10" inputmode="numeric" pattern="[0-9]{10}" autocomplete="off"
                               placeholder="۱۲۳۴۵۶۷۸۹۰">
                        <?php if ($msg = error('national_code')): ?>
                            <div class="invalid-feedback d-block"><?= e($msg) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" for="birth_date">تاریخ تولد (شمسی)</label>
                        <input type="text" id="birth_date" name="birth_date"
                               class="form-control form-control-lg <?= has_error('birth_date') ? 'is-invalid' : '' ?>"
                               value="<?= e(old('birth_date')) ?>"
                               placeholder="1370/01/01" inputmode="numeric" autocomplete="bday">
                        <?php if ($msg = error('birth_date')): ?>
                            <div class="invalid-feedback d-block"><?= e($msg) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-admin-primary w-100 mt-4 py-2">
                    <i class="bi bi-search me-1"></i>استعلام هویت
                </button>
            </form>
        </div>

        <?php elseif ($step === 2): ?>
        <!-- Step 2: Phone number + Shahkar -->
        <div class="form-section">
            <div class="alert alert-success mb-3">
                <i class="bi bi-check-circle me-2"></i>
                <strong><?= e(($registration['first_name'] ?? '') . ' ' . ($registration['last_name'] ?? '')) ?></strong>
                — هویت تأیید شد
                <?php if (!empty($registration['father_name'])): ?>
                    <span class="d-block small mt-1">نام پدر: <?= e($registration['father_name']) ?></span>
                <?php endif; ?>
            </div>
            <div class="form-section-title">
                <i class="bi bi-phone"></i>مرحله ۲: تأیید شماره موبایل
            </div>
            <form method="POST" action="<?= url('/admin/contacts/create/send-otp') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="phone">شماره موبایل</label>
                    <input type="tel" id="phone" name="phone"
                           class="form-control form-control-lg <?= has_error('phone') ? 'is-invalid' : '' ?>"
                           placeholder="09123456789" inputmode="tel" autocomplete="tel"
                           value="<?= e(old('phone')) ?>">
                    <?php if ($msg = error('phone')): ?>
                        <div class="invalid-feedback d-block"><?= e($msg) ?></div>
                    <?php endif; ?>
                    <div class="form-text">شماره موبایل باید به نام صاحب کد ملی باشد (شاهکار)</div>
                </div>
                <button type="submit" class="btn btn-admin-primary w-100 py-2">
                    <i class="bi bi-send me-1"></i>ارسال کد تأیید
                </button>
            </form>
        </div>

        <?php elseif ($step === 3): ?>
        <!-- Step 3: OTP verification -->
        <div class="form-section">
            <div class="alert alert-info mb-3">
                <i class="bi bi-chat-dots me-2"></i>
                کد تأیید به شماره <strong dir="ltr"><?= e($registration['phone'] ?? '') ?></strong> ارسال شد.
            </div>
            <div class="form-section-title">
                <i class="bi bi-shield-lock"></i>مرحله ۳: وارد کردن کد تأیید
            </div>
            <form method="POST" action="<?= url('/admin/contacts/create/verify-otp') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="otp_code">کد ۶ رقمی</label>
                    <input type="text" id="otp_code" name="otp_code"
                           class="form-control form-control-lg text-center <?= has_error('otp_code') ? 'is-invalid' : '' ?>"
                           maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                           autocomplete="one-time-code" style="letter-spacing: .5rem; font-size: 1.5rem;">
                    <?php if ($msg = error('otp_code')): ?>
                        <div class="invalid-feedback d-block"><?= e($msg) ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-admin-primary w-100 py-2">
                    <i class="bi bi-check-lg me-1"></i>تأیید کد
                </button>
            </form>
        </div>

        <?php elseif ($step === 4): ?>
        <!-- Step 4: Additional info and create -->
        <div class="form-section">
            <div class="alert alert-success mb-3">
                <i class="bi bi-check-circle me-2"></i>شماره موبایل تأیید شد.
            </div>
            <div class="form-section-title">
                <i class="bi bi-pencil-square"></i>مرحله ۴: اطلاعات تکمیلی (اختیاری)
            </div>
            <form method="POST" action="<?= url('/admin/contacts/create/store') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">نام</label>
                        <input type="text" class="form-control" value="<?= e($registration['first_name'] ?? '') ?>" disabled>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">نام خانوادگی</label>
                        <input type="text" class="form-control" value="<?= e($registration['last_name'] ?? '') ?>" disabled>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" for="email">ایمیل</label>
                        <input type="email" id="email" name="email"
                               class="form-control <?= has_error('email') ? 'is-invalid' : '' ?>"
                               value="<?= e(old('email')) ?>" placeholder="example@email.com">
                        <?php if ($msg = error('email')): ?>
                            <div class="invalid-feedback d-block"><?= e($msg) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold" for="contact_plan">پلن مشتری</label>
                        <select id="contact_plan" name="contact_plan" class="form-select">
                            <option value="">انتخاب کنید</option>
                            <option value="basic">پایه</option>
                            <option value="standard">استاندارد</option>
                            <option value="premium">ویژه</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-admin-primary w-100 mt-4 py-2">
                    <i class="bi bi-person-plus me-1"></i>ایجاد مخاطب در HubSpot
                </button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Cancel / Reset -->
        <?php if ($step > 1): ?>
        <div class="text-center mt-3">
            <a href="<?= url('/admin/contacts/create/reset') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-counterclockwise me-1"></i>شروع مجدد
            </a>
        </div>
        <?php endif; ?>

    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
