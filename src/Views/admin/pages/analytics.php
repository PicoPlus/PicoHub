<?php
/** @var array $stats */
$title = 'تحلیل‌ها';
$openDeals = max(0, $stats['deals'] - $stats['won_deals'] - $stats['lost_deals']);
ob_start();
?>

<div class="admin-page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2><i class="bi bi-graph-up me-2"></i>تحلیل‌ها و گزارش‌ها</h2>
        <p>نمودارها و آمار کلی عملکرد CRM</p>
    </div>
    <div class="refresh-indicator">
        <span class="live-dot"></span>
        <span>داده‌های لحظه‌ای</span>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card p-3 animate-in">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon purple"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($stats['contacts']) ?></div>
                    <div class="stat-label">کل مخاطبین</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card p-3 animate-in">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon blue"><i class="bi bi-briefcase-fill"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($stats['deals']) ?></div>
                    <div class="stat-label">کل معاملات</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card p-3 animate-in">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon green"><i class="bi bi-trophy-fill"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($stats['won_deals']) ?></div>
                    <div class="stat-label">معاملات موفق</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card p-3 animate-in">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon orange"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($openDeals) ?></div>
                    <div class="stat-label">در جریان</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <h6><i class="bi bi-pie-chart-fill me-2 text-primary"></i>توزیع وضعیت معاملات</h6>
            <canvas id="analyticsDonut" height="220"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="chart-card">
            <h6><i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>مقایسه عملکرد</h6>
            <canvas id="analyticsBar" height="220"></canvas>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-12">
        <div class="chart-card">
            <h6><i class="bi bi-graph-up-arrow me-2 text-primary"></i>روند رشد (شبیه‌سازی)</h6>
            <canvas id="analyticsLine" height="100"></canvas>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const won = <?= $stats['won_deals'] ?>;
    const lost = <?= $stats['lost_deals'] ?>;
    const open = <?= $openDeals ?>;
    const contacts = <?= $stats['contacts'] ?>;
    const deals = <?= $stats['deals'] ?>;

    // Doughnut
    new Chart(document.getElementById('analyticsDonut'), {
        type: 'doughnut',
        data: {
            labels: ['موفق', 'از دست رفته', 'در جریان'],
            datasets: [{ data: [won, lost, open], backgroundColor: ['#10b981','#ef4444','#6366f1'], borderWidth: 0, cutout: '65%' }]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { font: { family: 'Vazirmatn', size: 11 }, padding: 15 } } } }
    });

    // Bar comparison
    new Chart(document.getElementById('analyticsBar'), {
        type: 'bar',
        data: {
            labels: ['مخاطبین', 'معاملات', 'موفق', 'از دست رفته'],
            datasets: [{
                label: 'تعداد',
                data: [contacts, deals, won, lost],
                backgroundColor: ['rgba(99,102,241,.7)', 'rgba(59,130,246,.7)', 'rgba(16,185,129,.7)', 'rgba(239,68,68,.7)'],
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { family: 'Vazirmatn', size: 11 } } },
                y: { grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Vazirmatn', size: 10 } } }
            }
        }
    });

    // Line chart (simulated growth)
    new Chart(document.getElementById('analyticsLine'), {
        type: 'line',
        data: {
            labels: ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'],
            datasets: [{
                label: 'مخاطبین',
                data: Array.from({length:12}, (_,i) => Math.round(contacts * (i+1) / 12 + Math.random() * 3)),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,.05)',
                fill: true,
                tension: .4,
                pointRadius: 3,
            },{
                label: 'معاملات',
                data: Array.from({length:12}, (_,i) => Math.round(deals * (i+1) / 12 + Math.random() * 2)),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,.05)',
                fill: true,
                tension: .4,
                pointRadius: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'top', labels: { font: { family: 'Vazirmatn', size: 11 } } } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { family: 'Vazirmatn', size: 10 } } },
                y: { grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Vazirmatn', size: 10 } } }
            }
        }
    });
});
</script>
<?php $scripts = ob_get_clean(); ?>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';

