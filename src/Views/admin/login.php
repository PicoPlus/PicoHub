<?php
$title = 'ورود مدیر';
ob_start();
?>
<div class="auth-container">
    <div class="auth-card auth-form">
        <div class="auth-icon"><i class="bi bi-shield-lock"></i></div>
        <h1 class="auth-title">ورود مدیر</h1>

        <form method="POST" action="<?= url('/admin/login') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label" for="email">ایمیل</label>
                <input type="email" id="email" name="email"
                       class="form-control <?= has_error('email') ? 'is-invalid' : '' ?>"
                       value="<?= e(old('email')) ?>" autocomplete="username" inputmode="email">
                <?php if ($msg = error('email')): ?><div class="invalid-feedback d-block"><?= e($msg) ?></div><?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">رمز عبور</label>
                <input type="password" id="password" name="password" class="form-control" autocomplete="current-password">
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">مرا به خاطر بسپار</label>
            </div>
            <button type="submit" class="btn btn-primary w-100 auth-btn">ورود</button>
        </form>

        <div class="auth-footer mt-4 text-center">
            <a class="auth-link" href="<?= url('/auth/login') ?>">ورود کاربر</a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/app.php';
