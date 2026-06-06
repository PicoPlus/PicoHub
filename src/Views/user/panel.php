<?php
$title = 'پنل کاربری';
ob_start();
?>
<div class="page-shell user-panel-container">
    <header class="panel-header">
        <div class="panel-header__info">
            <h2 class="mb-0 text-break-word"><?= e($fullName ?: 'کاربر') ?></h2>
            <small class="text-muted d-block mt-1"><i class="bi bi-phone me-1"></i><?= e($phone) ?></small>
        </div>
        <form action="<?= url('/logout') ?>" method="POST" class="m-0">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-danger w-100-mobile">
                <i class="bi bi-box-arrow-left"></i> خروج
            </button>
        </form>
    </header>

    <?php if ($panel): ?>
        <div class="row g-2 g-md-3 mb-3 mb-md-4">
            <div class="col-6 col-lg-3">
                <div class="card stat-card-mobile h-100">
                    <div class="card-body">
                        <small class="text-muted">درآمد کل</small>
                        <h4><?= e($formatNumber($panel['stats']['total_revenue'] ?? 0)) ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card stat-card-mobile h-100">
                    <div class="card-body">
                        <small class="text-muted">معاملات باز</small>
                        <h4><?= e($formatNumber($panel['stats']['open_deals'] ?? 0)) ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card stat-card-mobile h-100">
                    <div class="card-body">
                        <small class="text-muted">معاملات بسته</small>
                        <h4><?= e($formatNumber($panel['stats']['closed_deals'] ?? 0)) ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card stat-card-mobile h-100">
                    <div class="card-body">
                        <small class="text-muted">کیف پول</small>
                        <h4><?= e($formatNumber($panel['stats']['wallet'] ?? 0)) ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm deals-section">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>معاملات من</strong>
                <a href="<?= url('/Deal/Search') ?>" class="btn btn-sm btn-outline-primary w-100-mobile">
                    <i class="bi bi-search me-1"></i>جستجو
                </a>
            </div>

            <!-- Mobile: card list -->
            <div class="card-body p-3 d-md-none">
                <?php if (!empty($panel['deals'])): ?>
                    <?php foreach ($panel['deals'] as $deal): ?>
                        <div class="deal-card-mobile">
                            <div class="deal-card-mobile__title"><?= e($deal['properties']['dealname'] ?? '-') ?></div>
                            <div class="deal-card-mobile__meta">
                                <span><i class="bi bi-flag me-1"></i><?= e($deal['properties']['dealstage'] ?? '-') ?></span>
                                <span class="deal-card-mobile__amount"><?= e($formatNumber($deal['properties']['amount'] ?? 0)) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted py-3 mb-0">معامله‌ای یافت نشد</p>
                <?php endif; ?>
            </div>

            <!-- Desktop: table -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>نام معامله</th>
                            <th>مرحله</th>
                            <th>مبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($panel['deals'])): ?>
                            <?php foreach ($panel['deals'] as $deal): ?>
                                <tr>
                                    <td class="text-break-word"><?= e($deal['properties']['dealname'] ?? '-') ?></td>
                                    <td><?= e($deal['properties']['dealstage'] ?? '-') ?></td>
                                    <td><?= e($formatNumber($deal['properties']['amount'] ?? 0)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">معامله‌ای یافت نشد</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">اطلاعات پنل کاربری بارگذاری نشد.</div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/app.php';
