<?php
$title = 'ثبت‌نام';
$step = $registration['step'] ?? 1;
ob_start();
?>
<div class="auth-container">
    <div class="auth-card auth-card-wide auth-form">
        <div class="auth-icon"><i class="bi bi-person-plus"></i></div>
        <h1 class="auth-title">ثبت‌نام در سامانه</h1>

        <div class="register-steps" role="list" aria-label="مراحل ثبت‌نام">
            <div class="register-step <?= $step >= 1 ? ($step > 1 ? 'done' : 'active') : '' ?>" role="listitem">۱. هویت</div>
            <div class="register-step <?= $step >= 2 ? ($step > 2 ? 'done' : 'active') : '' ?>" role="listitem">۲. موبایل</div>
            <div class="register-step <?= $step >= 3 ? 'active' : '' ?>" role="listitem">۳. تأیید</div>
        </div>

        <?php if ($step === 1): ?>
            <form method="POST" action="<?= url('/auth/register/verify-identity') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="national_code">کد ملی</label>
                        <input type="text" id="national_code" name="national_code"
                               class="form-control <?= has_error('national_code') ? 'is-invalid' : '' ?>"
                               value="<?= e(old('national_code', $registration['national_code'] ?? '')) ?>"
                               maxlength="10" inputmode="numeric" pattern="[0-9]{10}" autocomplete="off">
                        <?php if ($msg = error('national_code')): ?><div class="invalid-feedback d-block"><?= e($msg) ?></div><?php endif; ?>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="birth_date">تاریخ تولد (شمسی)</label>
                        <input type="text" id="birth_date" name="birth_date"
                               class="form-control <?= has_error('birth_date') ? 'is-invalid' : '' ?>"
                               value="<?= e(old('birth_date')) ?>" placeholder="1370/01/01" inputmode="numeric" autocomplete="bday">
                        <?php if ($msg = error('birth_date')): ?><div class="invalid-feedback d-block"><?= e($msg) ?></div><?php endif; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-4 auth-btn">استعلام هویت</button>
            </form>
        <?php elseif ($step === 2): ?>
            <div class="alert alert-success mb-3">
                <i class="bi bi-check-circle me-1"></i>
                <?= e(($registration['first_name'] ?? '') . ' ' . ($registration['last_name'] ?? '')) ?> — هویت تأیید شد
            </div>
            <form method="POST" action="<?= url('/auth/register/send-otp') ?>">
                <?= csrf_field() ?>
                <label class="form-label" for="phone">شماره موبایل</label>
                <input type="tel" id="phone" name="phone"
                       class="form-control <?= has_error('phone') ? 'is-invalid' : '' ?>"
                       placeholder="09123456789" inputmode="tel" autocomplete="tel">
                <?php if ($msg = error('phone')): ?><div class="invalid-feedback d-block"><?= e($msg) ?></div><?php endif; ?>
                <button type="submit" class="btn btn-primary w-100 mt-4 auth-btn">ارسال کد تأیید</button>
            </form>
        <?php else: ?>
            <form method="POST" action="<?= url('/auth/register/verify-otp') ?>">
                <?= csrf_field() ?>
                <label class="form-label" for="otp_code">کد تأیید پیامک‌شده</label>
                <input type="text" id="otp_code" name="otp_code"
                       class="form-control otp-input <?= has_error('otp_code') ? 'is-invalid' : '' ?>"
                       maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code">
                <?php if ($msg = error('otp_code')): ?><div class="invalid-feedback d-block"><?= e($msg) ?></div><?php endif; ?>
                <button type="submit" class="btn btn-success w-100 mt-4 auth-btn">تکمیل ثبت‌نام</button>
            </form>
        <?php endif; ?>

        <div class="auth-footer mt-4 text-center">
            <a class="auth-link" href="<?= url('/auth/login') ?>">قبلاً ثبت‌نام کرده‌اید؟ ورود</a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/app.php';
