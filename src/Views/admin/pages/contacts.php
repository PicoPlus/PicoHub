<?php
/** @var array $contacts */
$title = 'مخاطبین';
ob_start();
?>

<div class="admin-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2><i class="bi bi-people-fill me-2"></i>مخاطبین</h2>
        <p>مدیریت مخاطبین CRM — مشاهده، ویرایش و حذف</p>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-light text-dark align-self-center" style="font-size:.78rem"><?= count($contacts) ?> مخاطب</span>
        <a href="<?= url('/admin/contacts/create') ?>" class="btn btn-admin-primary">
            <i class="bi bi-person-plus me-1"></i>ثبت مخاطب جدید
        </a>
    </div>
</div>

<?php if ($flash = $_SESSION['flash'] ?? null): ?>
    <?php unset($_SESSION['flash']); ?>
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" style="border-radius:var(--radius-sm);font-size:.85rem">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (empty($contacts)): ?>
    <div class="empty-state">
        <i class="bi bi-people"></i>
        <h5>مخاطبی یافت نشد</h5>
        <p>هنوز مخاطبی ثبت نشده یا توکن HubSpot تنظیم نشده است.</p>
        <a href="<?= url('/admin/contacts/create') ?>" class="btn btn-admin-primary mt-3">
            <i class="bi bi-person-plus me-1"></i>ثبت اولین مخاطب
        </a>
    </div>
<?php else: ?>
    <div class="admin-table">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>نام</th>
                        <th>ایمیل</th>
                        <th>تلفن</th>
                        <th>کد ملی</th>
                        <th>تاریخ ایجاد</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $i => $contact): ?>
                        <?php $props = $contact['properties'] ?? []; ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= e(($props['firstname'] ?? '') . ' ' . ($props['lastname'] ?? '')) ?></strong></td>
                            <td dir="ltr"><?= e($props['email'] ?? '—') ?></td>
                            <td dir="ltr"><?= e($props['phone'] ?? '—') ?></td>
                            <td dir="ltr"><?= e($props['ncode'] ?? '—') ?></td>
                            <td dir="ltr" style="font-size:.78rem"><?= e(substr($props['createdate'] ?? '', 0, 10)) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editContact<?= $contact['id'] ?>" title="ویرایش">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="<?= url('/admin/contacts/delete') ?>" class="d-inline" onsubmit="return confirm('آیا از حذف مطمئنید؟')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($contact['id']) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editContact<?= $contact['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content" style="border-radius:var(--radius)">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>ویرایش مخاطب</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="<?= url('/admin/contacts/update') ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e($contact['id']) ?>">
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <label class="form-label">نام</label>
                                                    <input type="text" name="firstname" class="form-control" value="<?= e($props['firstname'] ?? '') ?>">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label">نام خانوادگی</label>
                                                    <input type="text" name="lastname" class="form-control" value="<?= e($props['lastname'] ?? '') ?>">
                                                </div>
                                            </div>
                                            <div class="mb-3 mt-3">
                                                <label class="form-label">ایمیل</label>
                                                <input type="email" name="email" class="form-control" dir="ltr" value="<?= e($props['email'] ?? '') ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">تلفن</label>
                                                <input type="text" name="phone" class="form-control" dir="ltr" value="<?= e($props['phone'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                                            <button type="submit" class="btn btn-admin-primary"><i class="bi bi-check-lg me-1"></i>ذخیره</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
