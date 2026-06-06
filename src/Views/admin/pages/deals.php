<?php
$title = 'معاملات';
ob_start();
?>
<div class="admin-page-header">
    <h2><i class="bi bi-briefcase me-2"></i>معاملات</h2>
    <p>لیست معاملات ثبت‌شده در HubSpot</p>
</div>

<?php if (!empty($deals)): ?>
<div class="admin-table">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>نام معامله</th>
                    <th>مرحله</th>
                    <th>مبلغ</th>
                    <th>تاریخ ایجاد</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deals as $deal): ?>
                    <?php $props = $deal['properties'] ?? []; ?>
                    <tr>
                        <td class="fw-semibold"><?= e($props['dealname'] ?? '—') ?></td>
                        <td>
                            <?php
                            $stage = $props['dealstage'] ?? '';
                            $badgeClass = 'new';
                            if (str_contains(strtolower($stage), 'closed') || str_contains($stage, 'won')) {
                                $badgeClass = 'won';
                            } elseif (str_contains(strtolower($stage), 'lost')) {
                                $badgeClass = 'lost';
                            } elseif ($stage !== '') {
                                $badgeClass = 'progress';
                            }
                            ?>
                            <span class="badge-stage <?= $badgeClass ?>"><?= e($stage ?: 'جدید') ?></span>
                        </td>
                        <td><?= e($props['amount'] ?? '—') ?></td>
                        <td class="text-muted small"><?= e(isset($props['createdate']) ? substr($props['createdate'], 0, 10) : '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="empty-state">
    <i class="bi bi-briefcase d-block"></i>
    <h5>معامله‌ای یافت نشد</h5>
    <p>هنوز معامله‌ای در HubSpot ثبت نشده یا ارتباط برقرار نیست.</p>
</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
