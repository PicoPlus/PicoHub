<?php
$title = 'تیکت‌ها';
ob_start();
?>
<div class="admin-page-header">
    <h2><i class="bi bi-ticket me-2"></i>تیکت‌ها</h2>
    <p>مدیریت تیکت‌های پشتیبانی</p>
</div>

<?php if (!empty($tickets)): ?>
<div class="admin-table">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>عنوان</th>
                    <th>وضعیت</th>
                    <th>اولویت</th>
                    <th>تاریخ ایجاد</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket): ?>
                    <?php $props = $ticket['properties'] ?? []; ?>
                    <tr>
                        <td class="fw-semibold"><?= e($props['subject'] ?? '—') ?></td>
                        <td>
                            <?php
                            $status = strtolower($props['hs_pipeline_stage'] ?? 'open');
                            $statusClass = 'open';
                            if (str_contains($status, 'closed') || str_contains($status, 'done')) {
                                $statusClass = 'closed';
                            } elseif (str_contains($status, 'pending') || str_contains($status, 'wait')) {
                                $statusClass = 'pending';
                            }
                            ?>
                            <span class="ticket-status <?= $statusClass ?>">
                                <i class="bi bi-circle-fill" style="font-size:.5rem"></i>
                                <?= e($props['hs_pipeline_stage'] ?? 'باز') ?>
                            </span>
                        </td>
                        <td><?= e($props['hs_ticket_priority'] ?? '—') ?></td>
                        <td class="text-muted small"><?= e(isset($props['createdate']) ? substr($props['createdate'], 0, 10) : '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="empty-state">
    <i class="bi bi-ticket d-block"></i>
    <h5>تیکتی یافت نشد</h5>
    <p>هنوز تیکتی ثبت نشده یا ارتباط با HubSpot برقرار نیست.</p>
</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
