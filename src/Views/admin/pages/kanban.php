<?php
$title = 'کانبان';
ob_start();
?>
<div class="admin-page-header">
    <h2><i class="bi bi-kanban me-2"></i>کانبان معاملات</h2>
    <p>نمای بصری مراحل معاملات</p>
</div>

<?php
$stages = [
    'new' => ['label' => 'جدید', 'class' => 'new', 'icon' => 'bi-plus-circle'],
    'progress' => ['label' => 'در حال پیگیری', 'class' => 'progress', 'icon' => 'bi-arrow-repeat'],
    'won' => ['label' => 'موفق', 'class' => 'won', 'icon' => 'bi-check-circle'],
    'lost' => ['label' => 'از دست رفته', 'class' => 'lost', 'icon' => 'bi-x-circle'],
];

$grouped = ['new' => [], 'progress' => [], 'won' => [], 'lost' => []];
foreach ($deals ?? [] as $deal) {
    $props = $deal['properties'] ?? [];
    $stage = strtolower($props['dealstage'] ?? '');
    if (str_contains($stage, 'closed') || str_contains($stage, 'won')) {
        $grouped['won'][] = $deal;
    } elseif (str_contains($stage, 'lost')) {
        $grouped['lost'][] = $deal;
    } elseif ($stage !== '') {
        $grouped['progress'][] = $deal;
    } else {
        $grouped['new'][] = $deal;
    }
}
?>

<div class="kanban-board">
    <?php foreach ($stages as $key => $stageInfo): ?>
        <div class="kanban-column">
            <div class="kanban-column-header">
                <h6><i class="bi <?= $stageInfo['icon'] ?> me-1"></i><?= $stageInfo['label'] ?></h6>
                <span class="badge bg-secondary rounded-pill"><?= count($grouped[$key]) ?></span>
            </div>
            <div class="kanban-column-body">
                <?php if (empty($grouped[$key])): ?>
                    <p class="text-muted text-center small mt-3">خالی</p>
                <?php else: ?>
                    <?php foreach ($grouped[$key] as $deal): ?>
                        <?php $props = $deal['properties'] ?? []; ?>
                        <div class="kanban-card">
                            <div class="card-title"><?= e($props['dealname'] ?? 'بدون نام') ?></div>
                            <div class="card-meta">
                                <?php if (!empty($props['amount'])): ?>
                                    <i class="bi bi-currency-dollar"></i> <?= e($props['amount']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($deals)): ?>
<div class="empty-state mt-3">
    <i class="bi bi-kanban d-block"></i>
    <h5>معامله‌ای برای نمایش وجود ندارد</h5>
    <p>معاملات پس از اتصال به HubSpot نمایش داده می‌شوند.</p>
</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
