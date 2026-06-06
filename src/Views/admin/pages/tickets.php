<?php
/** @var array $tickets */
$title = 'تیکت‌ها';
ob_start();
?>

<div class="admin-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2><i class="bi bi-ticket-detailed-fill me-2"></i>تیکت‌ها</h2>
        <p>مدیریت تیکت‌های پشتیبانی CRM</p>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-light text-dark align-self-center" style="font-size:.78rem"><?= count($tickets) ?> تیکت</span>
        <button class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
            <i class="bi bi-plus-lg me-1"></i>تیکت جدید
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

<?php if (empty($tickets)): ?>
    <div class="empty-state">
        <i class="bi bi-ticket-detailed"></i>
        <h5>تیکتی یافت نشد</h5>
        <p>هنوز تیکتی ثبت نشده یا توکن HubSpot تنظیم نشده است.</p>
    </div>
<?php else: ?>
    <div class="admin-table">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>موضوع</th>
                        <th>اولویت</th>
                        <th>وضعیت</th>
                        <th>تاریخ ایجاد</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $i => $ticket): ?>
                        <?php
                        $props = $ticket['properties'] ?? [];
                        $priority = strtolower($props['hs_ticket_priority'] ?? 'medium');
                        $priorityLabel = match($priority) {
                            'high' => 'بالا',
                            'low' => 'پایین',
                            default => 'متوسط',
                        };
                        $priorityClass = match($priority) {
                            'high' => 'badge bg-danger',
                            'low' => 'badge bg-secondary',
                            default => 'badge bg-warning text-dark',
                        };
                        ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= e($props['subject'] ?? '—') ?></strong></td>
                            <td><span class="<?= $priorityClass ?>" style="font-size:.7rem"><?= $priorityLabel ?></span></td>
                            <td>
                                <span class="ticket-status open"><?= e($props['hs_pipeline_stage'] ?? '—') ?></span>
                            </td>
                            <td dir="ltr" style="font-size:.78rem"><?= e(substr($props['createdate'] ?? '', 0, 10)) ?></td>
                            <td>
                                <form method="POST" action="<?= url('/admin/tickets/delete') ?>" class="d-inline" onsubmit="return confirm('آیا از حذف مطمئنید؟')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($ticket['id']) ?>">
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

<!-- Create Ticket Modal -->
<div class="modal fade" id="createTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:var(--radius)">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-ticket-detailed me-2"></i>ایجاد تیکت جدید</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('/admin/tickets/create') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">موضوع <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="content" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">اولویت</label>
                        <select name="hs_ticket_priority" class="form-select">
                            <option value="LOW">پایین</option>
                            <option value="MEDIUM" selected>متوسط</option>
                            <option value="HIGH">بالا</option>
                        </select>
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
