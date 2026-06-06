<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#6366f1">
    <meta name="mobile-web-app-capable" content="yes">
    <base href="<?= e(rtrim(url('/'), '/') . '/') ?>">
    <title><?= e($title ?? 'پیکوپلاس') ?> — PicoPlus</title>
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=vazirmatn:400,500,600,700" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.rtl.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="<?= asset('css/auth.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/mobile.css') ?>" rel="stylesheet">
    <?php if (!empty($extraStyles)): ?>
        <?= $extraStyles ?>
    <?php endif; ?>
</head>
<body>
    <?php $flash = flash_messages(); ?>
    <?php if (!empty($flash['success']) || !empty($flash['info'])): ?>
        <div class="flash-container">
            <?php if (!empty($flash['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    <?= e($flash['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="بستن"></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($flash['info'])): ?>
                <div class="alert alert-info alert-dismissible fade show mb-0" role="alert">
                    <?= e($flash['info']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="بستن"></button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?= $content ?? '' ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
