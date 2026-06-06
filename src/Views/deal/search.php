<?php
$title = 'جستجوی معامله';
ob_start();
?>
<div class="page-shell">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <h3 class="h4 h-md-3 mb-3 mb-md-4">جستجوی معامله با کد ملی</h3>
                    <form method="POST" action="<?= url('/Deal/Search') ?>">
                        <?= csrf_field() ?>
                        <div class="d-flex flex-column flex-sm-row gap-2 mb-3 mb-md-4 search-form-mobile">
                            <input type="text" name="national_code" class="form-control flex-grow-1"
                                   value="<?= e($query) ?>" maxlength="10" placeholder="کد ملی"
                                   inputmode="numeric" pattern="[0-9]{10}" autocomplete="off">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>جستجو
                            </button>
                        </div>
                    </form>

                    <?php if ($query): ?>
                        <h5 class="h6 h-md-5 mb-3">نتایج (<?= count($deals) ?>)</h5>
                        <?php if ($deals): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($deals as $deal): ?>
                                    <div class="list-group-item px-0">
                                        <strong class="d-block text-break-word"><?= e($deal['properties']['dealname'] ?? 'بدون نام') ?></strong>
                                        <small class="text-muted"><?= e($deal['properties']['dealstage'] ?? '') ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">معامله‌ای یافت نشد</p>
                        <?php endif; ?>
                    <?php endif; ?>

                    <a href="<?= url('/user/panel') ?>" class="btn btn-link mt-3 px-0">
                        <i class="bi bi-arrow-right me-1"></i>بازگشت به پنل
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/app.php';
