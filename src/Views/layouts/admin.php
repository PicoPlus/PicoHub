<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1e293b">
    <base href="<?= e(rtrim(url('/'), '/') . '/') ?>">
    <title><?= e($title ?? 'پنل مدیریت') ?> — PicoPlus</title>
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=vazirmatn:400,500,600,700" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.rtl.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/mobile.css') ?>" rel="stylesheet">
</head>
<body>
    <?php
    $currentPath = \App\Support\AppPath::normalizeRequestPath(
        parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'
    );
    $navClass = static function (string $path) use ($currentPath) {
        if ($path === $currentPath) return 'active';
        if ($path !== '/admin/contacts/create' && str_starts_with($currentPath, $path) && !str_starts_with($currentPath, $path . '/create')) return 'active';
        return '';
    };
    $navItems = [
        ['/admin/dashboard', 'bi-speedometer2', 'داشبورد'],
        ['/admin/contacts/create', 'bi-person-plus', 'ثبت مخاطب'],
        ['/admin/kanban', 'bi-kanban', 'کانبان'],
        ['/admin/contacts', 'bi-people', 'مخاطبین'],
        ['/admin/deals', 'bi-briefcase', 'معاملات'],
        ['/admin/tickets', 'bi-ticket', 'تیکت‌ها'],
        ['/admin/analytics', 'bi-graph-up', 'تحلیل‌ها'],
        ['/admin/settings', 'bi-gear', 'تنظیمات'],
    ];
    ?>

    <!-- Mobile top bar -->
    <header class="admin-mobile-topbar d-md-none">
        <button class="admin-menu-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminNav" aria-label="منو">
            <i class="bi bi-list"></i>
        </button>
        <strong class="text-truncate">پیکوپلاس</strong>
        <form action="<?= url('/admin/logout') ?>" method="POST" class="m-0">
            <?= csrf_field() ?>
            <button type="submit" class="admin-menu-btn" aria-label="خروج"><i class="bi bi-box-arrow-left"></i></button>
        </form>
    </header>

    <!-- Mobile offcanvas nav -->
    <div class="offcanvas offcanvas-end admin-offcanvas d-md-none" tabindex="-1" id="adminNav">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-white">منوی مدیریت</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="بستن"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <nav class="nav flex-column flex-grow-1">
                <?php foreach ($navItems as [$path, $icon, $label]): ?>
                    <a class="nav-link <?= $navClass($path) ?>" href="<?= url($path) ?>">
                        <i class="bi <?= e($icon) ?> me-2"></i><?= e($label) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <form action="<?= url('/admin/logout') ?>" method="POST" class="mt-3">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline-light w-100"><i class="bi bi-box-arrow-left me-1"></i>خروج</button>
            </form>
        </div>
    </div>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <aside class="col-md-3 col-lg-2 admin-sidebar p-3 d-none d-md-block">
                <div class="sidebar-brand"><i class="bi bi-grid-1x2-fill"></i>پیکوپلاس</div>
                <nav class="nav flex-column">
                    <?php foreach ($navItems as [$path, $icon, $label]): ?>
                        <a class="nav-link <?= $navClass($path) ?>" href="<?= url($path) ?>">
                            <i class="bi <?= e($icon) ?> me-2"></i><?= e($label) ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <form action="<?= url('/admin/logout') ?>" method="POST" class="mt-4">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-light btn-sm w-100"><i class="bi bi-box-arrow-left me-1"></i>خروج</button>
                </form>
            </aside>
            <main class="col-md-9 col-lg-10 admin-content admin-content-mobile">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
