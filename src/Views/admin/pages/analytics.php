<?php
$title = 'تحلیل‌ها';
ob_start();
?>
<div class="admin-page-header">
    <h2><i class="bi bi-graph-up me-2"></i>تحلیل‌ها</h2>
    <p>آمار و گزارش‌های کلی سیستم</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center py-4">
                <div class="stat-icon purple mx-auto mb-2"><i class="bi bi-people"></i></div>
                <div class="stat-value"><?= (int) ($stats['contacts'] ?? 0) ?></div>
                <div class="stat-label">کل مخاطبین</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center py-4">
                <div class="stat-icon green mx-auto mb-2"><i class="bi bi-briefcase"></i></div>
                <div class="stat-value"><?= (int) ($stats['deals'] ?? 0) ?></div>
                <div class="stat-label">کل معاملات</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center py-4">
                <div class="stat-icon blue mx-auto mb-2"><i class="bi bi-check2-all"></i></div>
                <div class="stat-value"><?= (int) ($stats['won_deals'] ?? 0) ?></div>
                <div class="stat-label">معاملات موفق</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center py-4">
                <div class="stat-icon red mx-auto mb-2"><i class="bi bi-x-circle"></i></div>
                <div class="stat-value"><?= (int) ($stats['lost_deals'] ?? 0) ?></div>
                <div class="stat-label">معاملات ازدست‌رفته</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="chart-placeholder">
            <i class="bi bi-bar-chart-line display-4 text-muted mb-3"></i>
            <h6 class="text-muted">نمودار معاملات</h6>
            <p class="text-muted small">نمودار بصری پس از اتصال به HubSpot و دریافت داده‌های کافی نمایش داده می‌شود.</p>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="chart-placeholder">
            <i class="bi bi-pie-chart display-4 text-muted mb-3"></i>
            <h6 class="text-muted">توزیع مراحل</h6>
            <p class="text-muted small">با افزایش داده‌ها این نمودار فعال خواهد شد.</p>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
