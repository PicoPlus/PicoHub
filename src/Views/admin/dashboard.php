<?php
$title = 'داشبورد';
ob_start();
?>
<div class="admin-page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
    <div>
        <h2>داشبورد مدیریت</h2>
        <p>خوش آمدید <?= e(session('user_name', 'Admin')) ?></p>
    </div>
    <?php if ($owner = session('admin_owner')): ?>
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
            <i class="bi bi-person-badge me-1"></i>مالک: <?= e($owner['name'] ?? '') ?>
        </span>
    <?php endif; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon purple"><i class="bi bi-people"></i></div>
                <div>
                    <div class="stat-value"><?= (int) ($stats['contacts'] ?? 0) ?></div>
                    <div class="stat-label">مخاطبین</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon green"><i class="bi bi-briefcase"></i></div>
                <div>
                    <div class="stat-value"><?= (int) ($stats['deals'] ?? 0) ?></div>
                    <div class="stat-label">معاملات</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon blue"><i class="bi bi-cloud-check"></i></div>
                <div>
                    <div class="stat-value text-success" style="font-size:1.1rem"><i class="bi bi-check-circle-fill"></i></div>
                    <div class="stat-label">HubSpot متصل</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon orange"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="stat-value" style="font-size:1rem"><?= date('H:i') ?></div>
                    <div class="stat-label">آخرین به‌روزرسانی</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card stat-card">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-lightning me-1 text-warning"></i>دسترسی سریع</h6>
                <div class="row g-2">
                    <div class="col-6 col-md-4">
                        <a href="<?= url('/admin/contacts/create') ?>" class="btn btn-outline-primary w-100 py-2">
                            <i class="bi bi-person-plus d-block mb-1" style="font-size:1.25rem"></i>
                            <small>ثبت مخاطب</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="<?= url('/admin/contacts') ?>" class="btn btn-outline-secondary w-100 py-2">
                            <i class="bi bi-people d-block mb-1" style="font-size:1.25rem"></i>
                            <small>مخاطبین</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="<?= url('/admin/deals') ?>" class="btn btn-outline-secondary w-100 py-2">
                            <i class="bi bi-briefcase d-block mb-1" style="font-size:1.25rem"></i>
                            <small>معاملات</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-1 text-info"></i>اطلاعات سیستم</h6>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">نسخه PHP</span>
                        <span class="fw-semibold"><?= PHP_VERSION ?></span>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">سرور</span>
                        <span class="fw-semibold"><?= e(php_uname('s')) ?></span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">تاریخ</span>
                        <span class="fw-semibold"><?= date('Y/m/d') ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
