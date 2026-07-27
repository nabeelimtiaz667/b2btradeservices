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
    <link href="https://fonts.googleapis.com/css2?family=Zalando+Sans:ital,wdth,wght@0,75..125,200..900;1,75..125,200..900&display=swap" rel="stylesheet">
    <title><?= ($title ?? 'Home') . ' | ' . ($siteSettings['site_name'] ?? 'B2B Trade Services') ?></title>
    <?php $metaDesc = $siteSettings['meta_description'] ?? ''; if ($metaDesc): ?>
    <meta name="description" content="<?= esc($metaDesc) ?>">
    <?php endif; ?>
    <?php $metaKw = $siteSettings['meta_keywords'] ?? ''; if ($metaKw): ?>
    <meta name="keywords" content="<?= esc($metaKw) ?>">
    <?php endif; ?>
    <style>.register-your-company { top: 70%; }</style>
</head>
<body>
    <?= $this->include('partials/header-profile') ?>

    <?= $this->renderSection('content') ?>

    <?= $this->include('partials/footer') ?>

<script>
    if ($('.supplier-profile-slider-sec').length) {
        $('.supplier-profile-slider-sec').slick({
            autoplay: false,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplaySpeed: 2000,
            speed: 1200,
            infinite: true,
            arrows: false,
            dots: true,
            fade: false
        });
    }
    if ($('.common-slider').length) {
        $('.common-slider').slick({
            autoplay: false,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplaySpeed: 2000,
            speed: 1200,
            infinite: true,
            arrows: false,
            dots: true,
            fade: false
        });
    }
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        $('.common-slider').slick('setPosition');
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>
<?php $gaId = $siteSettings['google_analytics_id'] ?? ''; if ($gaId): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= esc($gaId) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= esc($gaId) ?>');</script>
<?php endif; ?>
<?php $gtmId = $siteSettings['google_tag_manager_id'] ?? ''; if ($gtmId): ?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= esc($gtmId) ?>');</script>
<?php endif; ?>
</body>
</html>
