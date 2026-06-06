<?php
$title = 'ورود';
ob_start();
?>
<div class="auth-container">
    <div class="auth-card auth-form">
        <div class="auth-icon"><i class="bi bi-person-circle"></i></div>
        <h1 class="auth-title">ورود به سامانه</h1>
        <p class="auth-subtitle">کد ملی خود را وارد کنید</p>

        <form method="POST" action="<?= url('/auth/login') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label" for="national_code">کد ملی</label>
                <input type="text" id="national_code" name="national_code"
                       class="form-control <?= has_error('national_code') ? 'is-invalid' : '' ?>"
                       value="<?= e(old('national_code')) ?>" maxlength="10" inputmode="numeric"
                       pattern="[0-9]{10}" autocomplete="off" autofocus>
                <?php if ($msg = error('national_code')): ?><div class="invalid-feedback d-block"><?= e($msg) ?></div><?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="form-label" for="role">نقش</label>
                <select id="role" name="role" class="form-select">
                    <option value="User" <?= old('role', $selectedRole) === 'User' ? 'selected' : '' ?>>کاربر</option>
                    <option value="Admin" <?= old('role', $selectedRole) === 'Admin' ? 'selected' : '' ?>>مدیر</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100 auth-btn auth-btn-primary">ورود</button>
        </form>

        <div class="auth-footer mt-4 text-center">
            <a class="auth-link" href="<?= url('/auth/register') ?>">حساب کاربری ندارید؟ ثبت‌نام کنید</a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/app.php';
