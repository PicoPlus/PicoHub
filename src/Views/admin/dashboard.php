<?php
$title = 'داشبورد';
ob_start();
?>
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3 mb-md-4">
    <div>
        <h2 class="h4 h-md-2 mb-1">داشبورد مدیریت</h2>
        <p class="text-muted mb-0 small">خوش آمدید <?= e(session('user_name', 'Admin')) ?></p>
    </div>
    <?php if ($owner = session('admin_owner')): ?>
        <span class="badge bg-primary text-wrap">مالک: <?= e($owner['name'] ?? '') ?></span>
    <?php endif; ?>
</div>

<div class="row g-2 g-md-4">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100 stat-card-mobile">
            <div class="card-body">
                <h6 class="text-muted small">مخاطبین (نمونه)</h6>
                <h3 class="mb-0"><?= (int) ($stats['contacts'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100 stat-card-mobile">
            <div class="card-body">
                <h6 class="text-muted small">معاملات (نمونه)</h6>
                <h3 class="mb-0"><?= (int) ($stats['deals'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100 stat-card-mobile">
            <div class="card-body">
                <h6 class="text-muted small">وضعیت HubSpot</h6>
                <h3 class="text-success h5 h-md-3 mb-0"><i class="bi bi-check-circle"></i> متصل</h3>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
