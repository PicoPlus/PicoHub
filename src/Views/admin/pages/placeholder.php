<?php
ob_start();
?>
<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5 text-center">
        <i class="bi bi-tools display-4 text-muted mb-3"></i>
        <h3><?= e($title) ?></h3>
        <p class="text-muted">این بخش در نسخه PHP در حال توسعه است.</p>
        <a href="<?= url('/admin/dashboard') ?>" class="btn btn-outline-primary">بازگشت به داشبورد</a>
    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
