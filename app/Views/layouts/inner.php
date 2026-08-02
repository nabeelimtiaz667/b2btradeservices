<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= base_url('assets/images/b2b-icon.svg') ?>" rel="icon">
    <link href="<?= base_url('assets/images/b2b-icon.svg') ?>" rel="apple-touch-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css" />
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Zalando+Sans:ital,wdth,wght@0,75..125,200..900;1,75..125,200..900&display=swap"
        rel="stylesheet">

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

    <title><?= ($title ?? 'Home') . ' | ' . ($siteSettings['site_name'] ?? 'B2B Trade Services') ?></title>
    <?php $metaDesc = $metaDescription ?? $siteSettings['meta_description'] ?? '';
    if ($metaDesc): ?>
    <meta name="description" content="<?= esc($metaDesc) ?>">
    <?php endif; ?>
    <?php $metaKw = $siteSettings['meta_keywords'] ?? '';
    if ($metaKw): ?>
    <meta name="keywords" content="<?= esc($metaKw) ?>">
    <?php endif; ?>
    <?php if (!empty($canonical)): ?>
    <link rel="canonical" href="<?= esc($canonical) ?>">
    <?php endif; ?>
    <?= $this->renderSection('styles') ?>
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API = Tawk_API || {},
        Tawk_LoadStart = new Date();
    (function() {
        var s1 = document.createElement("script"),
            s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/69c9f17b36b7e41c2bf1d276/1jkudeil8';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
    </script>
    <!--End of Tawk.to Script-->
</head>

<body>
    <?= $this->include('partials/header-inner') ?>

    <?= $this->renderSection('content') ?>

    <?= $this->include('partials/footer') ?>
    <?php $gaId = $siteSettings['google_analytics_id'] ?? '';
    if ($gaId): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= esc($gaId) ?>"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', '<?= esc($gaId) ?>');
    </script>
    <?php endif; ?>
    <?php $gtmId = $siteSettings['google_tag_manager_id'] ?? '';
    if ($gtmId): ?>
    <script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', '<?= esc($gtmId) ?>');
    </script>
    <?php endif; ?>
</body>

</html>