<?php
/** @var array $deals */
$title = 'معاملات';
ob_start();
?>

<div class="admin-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2><i class="bi bi-briefcase-fill me-2"></i>معاملات</h2>
        <p>مدیریت معاملات CRM — ایجاد، مشاهده و حذف</p>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-light text-dark align-self-center" style="font-size:.78rem"><?= count($deals) ?> معامله</span>
        <button class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#createDealModal">
            <i class="bi bi-plus-lg me-1"></i>معامله جدید
        </button>
    </div>
</div>

<?php if ($flash = $_SESSION['flash'] ?? null): ?>
    <?php unset($_SESSION['flash']); ?>
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" style="border-radius:var(--radius-sm);font-size:.85rem">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (empty($deals)): ?>
    <div class="empty-state">
        <i class="bi bi-briefcase"></i>
        <h5>معامله‌ای یافت نشد</h5>
        <p>هنوز معامله‌ای ثبت نشده یا توکن HubSpot تنظیم نشده است.</p>
    </div>
<?php else: ?>
    <div class="admin-table">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>نام معامله</th>
                        <th>مبلغ</th>
                        <th>مرحله</th>
                        <th>پایپلاین</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deals as $i => $deal): ?>
                        <?php
                        $props = $deal['properties'] ?? [];
                        $stage = strtolower($props['dealstage'] ?? '');
                        $stageClass = 'progress';
                        if (str_contains($stage, 'won') || str_contains($stage, 'closed')) $stageClass = 'won';
                        elseif (str_contains($stage, 'lost')) $stageClass = 'lost';
                        elseif (str_contains($stage, 'new') || str_contains($stage, 'appointment')) $stageClass = 'new';
                        ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= e($props['dealname'] ?? '—') ?></strong></td>
                            <td dir="ltr"><?= e($props['amount'] ?? '—') ?></td>
                            <td><span class="badge-stage <?= $stageClass ?>"><?= e($props['dealstage'] ?? '—') ?></span></td>
                            <td><?= e($props['pipeline'] ?? 'default') ?></td>
                            <td>
                                <form method="POST" action="<?= url('/admin/deals/delete') ?>" class="d-inline" onsubmit="return confirm('آیا از حذف مطمئنید؟')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($deal['id']) ?>">
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

<!-- Create Deal Modal -->
<div class="modal fade" id="createDealModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:var(--radius)">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-briefcase me-2"></i>ایجاد معامله جدید</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('/admin/deals/create') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نام معامله <span class="text-danger">*</span></label>
                        <input type="text" name="dealname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">مبلغ</label>
                        <input type="number" name="amount" class="form-control" dir="ltr">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">مرحله</label>
                        <select name="dealstage" class="form-select">
                            <option value="appointmentscheduled">قرار ملاقات</option>
                            <option value="qualifiedtobuy">واجد شرایط</option>
                            <option value="presentationscheduled">ارائه</option>
                            <option value="decisionmakerboughtin">تصمیم‌گیری</option>
                            <option value="contractsent">ارسال قرارداد</option>
                            <option value="closedwon">بسته شده - موفق</option>
                            <option value="closedlost">بسته شده - از دست رفته</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">پایپلاین</label>
                        <input type="text" name="pipeline" class="form-control" value="default" dir="ltr">
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
