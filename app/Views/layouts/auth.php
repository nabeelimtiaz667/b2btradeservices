<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= base_url('assets/images/b2b-icon.svg') ?>" rel="icon">
    <link href="<?= base_url('assets/images/b2b-icon.svg') ?>" rel="apple-touch-icon">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="<?= base_url('assets/css/login-style.css') ?>" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Zalando+Sans:ital,wdth,wght@0,75..125,200..900;1,75..125,200..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css" />

    <link rel="apple-touch-icon" sizes="57x57" href="<?= base_url('assets/site-identity/apple-icon-57x57.png') ?>">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= base_url('assets/site-identity/apple-icon-60x60.png') ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= base_url('assets/site-identity/apple-icon-72x72.png') ?>">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= base_url('assets/site-identity/apple-icon-76x76.png') ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= base_url('assets/site-identity/apple-icon-114x114.png') ?>">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= base_url('assets/site-identity/apple-icon-120x120.png') ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= base_url('assets/site-identity/apple-icon-144x144.png') ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= base_url('assets/site-identity/apple-icon-152x152.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/site-identity/apple-icon-180x180.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192"
        href="<?= base_url('assets/site-identity/android-icon-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/site-identity/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= base_url('assets/site-identity/favicon-96x96.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/site-identity/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= base_url('assets/site-identity/manifest.json') ?>">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= base_url('assets/site-identity/ms-icon-144x144.png') ?>">
    <meta name="theme-color" content="#ffffff">

    <title><?= esc($title ?? ($siteSettings['site_name'] ?? 'B2B Trade Services')) ?></title>
    <?php $metaDesc = $metaDescription ?? $siteSettings['meta_description'] ?? '';
    if ($metaDesc): ?>
    <meta name="description" content="<?= esc($metaDesc) ?>">
    <?php endif; ?>
    <?php if (!empty($canonical)): ?>
    <link rel="canonical" href="<?= esc($canonical) ?>">
    <?php endif; ?>
</head>

<body>
    <?= $this->renderSection('content') ?>

    <footer>
        <div class="container">
            <p class="mb-0">
                <?= esc($siteSettings['footer_text'] ?? 'Copyrights © 2026 b2btradeservices.com All Rights Reserved.') ?>
            </p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>