<?php
/** @var array $companies */
/** @var string|null $error */
$title = 'شرکت‌ها';
ob_start();
?>

<div class="admin-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2><i class="bi bi-building me-2"></i>شرکت‌ها</h2>
        <p>مدیریت شرکت‌ها در HubSpot CRM</p>
    </div>
    <button class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#createCompanyModal">
        <i class="bi bi-plus-lg me-1"></i>شرکت جدید
    </button>
</div>

<?php if ($flash = $_SESSION['flash'] ?? null): ?>
    <?php unset($_SESSION['flash']); ?>
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" style="border-radius:var(--radius-sm);font-size:.85rem">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-warning" style="border-radius:var(--radius-sm);font-size:.85rem">
        <i class="bi bi-exclamation-triangle me-1"></i><?= e($error) ?>
    </div>
<?php endif; ?>

<?php if (empty($companies)): ?>
    <div class="empty-state">
        <i class="bi bi-building"></i>
        <h5>شرکتی یافت نشد</h5>
        <p>هنوز شرکتی در CRM ثبت نشده است یا توکن HubSpot تنظیم نشده.</p>
    </div>
<?php else: ?>
    <div class="admin-table">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>نام شرکت</th>
                        <th>دامنه</th>
                        <th>تلفن</th>
                        <th>صنعت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($companies as $i => $company): ?>
                        <?php $props = $company['properties'] ?? []; ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= e($props['name'] ?? '—') ?></strong></td>
                            <td dir="ltr"><?= e($props['domain'] ?? '—') ?></td>
                            <td dir="ltr"><?= e($props['phone'] ?? '—') ?></td>
                            <td><?= e($props['industry'] ?? '—') ?></td>
                            <td>
                                <form method="POST" action="<?= url('/admin/companies/delete') ?>" class="d-inline" onsubmit="return confirm('آیا از حذف مطمئنید؟')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($company['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Create Company Modal -->
<div class="modal fade" id="createCompanyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:var(--radius)">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-building me-2"></i>ایجاد شرکت جدید</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('/admin/companies/create') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نام شرکت <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">دامنه</label>
                        <input type="text" name="domain" class="form-control" dir="ltr" placeholder="example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تلفن</label>
                        <input type="text" name="phone" class="form-control" dir="ltr">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">صنعت</label>
                        <input type="text" name="industry" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-admin-primary"><i class="bi bi-check-lg me-1"></i>ایجاد</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
