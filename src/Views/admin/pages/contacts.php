<?php
$title = 'مخاطبین';
ob_start();
?>
<div class="admin-page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
    <div>
        <h2><i class="bi bi-people me-2"></i>مخاطبین</h2>
        <p>مدیریت مخاطبین HubSpot</p>
    </div>
    <a href="<?= url('/admin/contacts/create') ?>" class="btn btn-admin-primary">
        <i class="bi bi-person-plus me-1"></i>ثبت مخاطب جدید
    </a>
</div>

<?php if (!empty($contacts)): ?>
<div class="admin-table">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>نام</th>
                    <th>کد ملی</th>
                    <th>تلفن</th>
                    <th>ایمیل</th>
                    <th>تاریخ ایجاد</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact): ?>
                    <?php $props = $contact['properties'] ?? []; ?>
                    <tr>
                        <td class="fw-semibold">
                            <?= e(trim(($props['firstname'] ?? '') . ' ' . ($props['lastname'] ?? '')) ?: '—') ?>
                        </td>
                        <td><code><?= e($props['ncode'] ?? '—') ?></code></td>
                        <td><?= e($props['phone'] ?? '—') ?></td>
                        <td><?= e($props['email'] ?? '—') ?></td>
                        <td class="text-muted small"><?= e(isset($props['createdate']) ? substr($props['createdate'], 0, 10) : '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="empty-state">
    <i class="bi bi-people d-block"></i>
    <h5>مخاطبی یافت نشد</h5>
    <p>هنوز مخاطبی در سیستم ثبت نشده یا ارتباط با HubSpot برقرار نیست.</p>
    <a href="<?= url('/admin/contacts/create') ?>" class="btn btn-admin-primary mt-2">
        <i class="bi bi-person-plus me-1"></i>ثبت اولین مخاطب
    </a>
</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
