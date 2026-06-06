<?php
/** @var array $adminUsers */
/** @var string $currentAdmin */
/** @var array $recentContacts */
/** @var string|null $error */
$title = 'مدیریت کاربران';
ob_start();
?>

<div class="admin-page-header">
    <h2><i class="bi bi-person-gear me-2"></i>مدیریت کاربران</h2>
    <p>مدیریت دسترسی ادمین‌ها و کاربران سیستم</p>
</div>

<!-- Admin Users Section -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="form-section">
            <div class="form-section-title"><i class="bi bi-shield-lock-fill"></i>مدیران سیستم</div>
            <p class="text-muted" style="font-size:.82rem">لیست حساب‌های ادمین — برای تغییر از فایل <code>.env</code> و <code>config/config.php</code> استفاده کنید.</p>

            <div class="row g-3">
                <?php foreach ($adminUsers as $email => $password): ?>
                    <div class="col-md-4">
                        <div class="user-card">
                            <div class="user-avatar">
                                <?= strtoupper(substr($email, 0, 1)) ?>
                            </div>
                            <div class="user-info">
                                <div class="user-name"><?= e($email) ?></div>
                                <div class="user-meta">
                                    <?php if ($email === $currentAdmin): ?>
                                        <span class="settings-status connected"><i class="bi bi-check-circle-fill"></i> آنلاین</span>
                                    <?php else: ?>
                                        <span class="text-muted">آفلاین</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Session Info -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="form-section">
            <div class="form-section-title"><i class="bi bi-person-check-fill"></i>نشست فعلی</div>
            <div class="config-group">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <label class="form-label mb-0">ایمیل ادمین</label>
                        <div style="font-size:.85rem;font-weight:600" dir="ltr"><?= e($currentAdmin) ?></div>
                    </div>
                    <span class="live-dot"></span>
                </div>
            </div>
            <div class="config-group mt-2">
                <label class="form-label mb-0">شناسه نشست</label>
                <div style="font-size:.75rem;font-weight:500;color:var(--text-muted)" dir="ltr"><?= e(session_id()) ?></div>
            </div>
            <div class="config-group mt-2">
                <label class="form-label mb-0">IP فعلی</label>
                <div style="font-size:.82rem;font-weight:600" dir="ltr"><?= e($_SERVER['REMOTE_ADDR'] ?? '—') ?></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-section">
            <div class="form-section-title"><i class="bi bi-info-circle-fill"></i>امنیت و دسترسی</div>
            <div class="alert alert-info" style="font-size:.82rem;border-radius:var(--radius-sm)">
                <i class="bi bi-shield-check me-1"></i>
                تمام درخواست‌ها با توکن CSRF محافظت می‌شوند.
            </div>
            <div class="alert alert-light" style="font-size:.82rem;border-radius:var(--radius-sm)">
                <strong>نکات امنیتی:</strong>
                <ul class="mb-0 mt-1" style="font-size:.78rem">
                    <li>برای تغییر رمز عبور ادمین، فایل <code>.env</code> را ویرایش کنید</li>
                    <li>برای افزودن ادمین جدید، <code>config/config.php</code> را ویرایش کنید</li>
                    <li>نشست‌ها بعد از بسته شدن مرورگر منقضی می‌شوند</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Recent Registered Users (from HubSpot) -->
<div class="form-section">
    <div class="form-section-title"><i class="bi bi-people-fill"></i>آخرین کاربران ثبت‌نام شده</div>
    <p class="text-muted" style="font-size:.82rem">کاربرانی که اخیراً از طریق فرم ثبت‌نام وارد سیستم شده‌اند (از HubSpot)</p>

    <?php if ($error): ?>
        <div class="alert alert-warning" style="font-size:.82rem;border-radius:var(--radius-sm)">
            <i class="bi bi-exclamation-triangle me-1"></i><?= e($error) ?>
        </div>
    <?php elseif (empty($recentContacts)): ?>
        <div class="empty-state" style="padding:2rem">
            <i class="bi bi-people" style="font-size:2rem"></i>
            <p class="mt-2">هنوز کاربری ثبت‌نام نکرده یا توکن HubSpot تنظیم نشده است.</p>
        </div>
    <?php else: ?>
        <div class="admin-table">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>تلفن</th>
                            <th>کد ملی</th>
                            <th>تاریخ ثبت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($recentContacts, 0, 10) as $i => $contact): ?>
                            <?php $props = $contact['properties'] ?? []; ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= e(($props['firstname'] ?? '') . ' ' . ($props['lastname'] ?? '')) ?></td>
                                <td dir="ltr"><?= e($props['phone'] ?? '—') ?></td>
                                <td dir="ltr"><?= e($props['ncode'] ?? '—') ?></td>
                                <td dir="ltr" style="font-size:.78rem"><?= e(substr($props['createdate'] ?? '', 0, 10)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include BASE_PATH . '/src/Views/layouts/admin.php';
