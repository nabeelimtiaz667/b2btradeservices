<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc(($title ?? 'Email Verification') . ' - B2B Trade Services') ?></title>
    <?php if (!empty($metaDescription)): ?>
    <meta name="description" content="<?= esc($metaDescription) ?>">
    <?php endif; ?>
    <meta name="robots" content="noindex">
    <link href="<?= base_url('assets/images/b2b-icon.svg') ?>" rel="icon">
    <link href="<?= base_url('assets/images/b2b-icon.svg') ?>" rel="apple-touch-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }
        .blur-circle {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            z-index: 0;
        }
        .blur-circle-1 { width: 400px; height: 400px; background: rgba(0,0,0,0.15); bottom: -100px; right: 10%; }
        .blur-circle-2 { width: 350px; height: 350px; background: rgba(0,0,0,0.1); bottom: 50px; right: 25%; }
        .auth-container { position: relative; z-index: 1; width: 100%; max-width: 480px; padding: 20px; }
        .glass-form {
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.8);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            text-align: center;
        }
        .result-icon { font-size: 40px; margin-bottom: 20px; }
        .result-icon.ok { color: #15A2A0; }
        .result-icon.warn { color: #dc3545; }
        h1 { font-size: 24px; font-weight: 700; color: #0A504F; margin-bottom: 12px; }
        p.msg { font-size: 15px; color: #555; margin-bottom: 28px; }
        .gradeint-cta {
            background-image: linear-gradient(45deg, #15A2A0, #5FC86B);
            display: inline-flex;
            border-radius: 26px;
            color: #fff;
            font-size: 14px;
            padding: 14px 32px;
            border: 0;
            text-decoration: none;
            font-weight: 600;
        }
        .gradeint-cta:hover { color: #fff; opacity: 0.92; }
    </style>
</head>
<body>
    <div class="blur-circle blur-circle-1"></div>
    <div class="blur-circle blur-circle-2"></div>

    <div class="auth-container">
        <div class="glass-form">
            <div class="b2b-logo" style="margin-bottom:24px;">
                <a href="<?= base_url() ?>"><img src="<?= base_url('assets/images/logo.svg') ?>" width="180px"></a>
            </div>
            <div class="result-icon <?= !empty($showLogin) ? 'warn' : (stripos($title ?? '', 'expired') !== false || stripos($title ?? '', 'invalid') !== false ? 'warn' : 'ok') ?>">
                <i class="fas <?= (!empty($showLogin) || stripos($title ?? '', 'expired') !== false || stripos($title ?? '', 'invalid') !== false) ? 'fa-circle-exclamation' : 'fa-circle-check' ?>"></i>
            </div>
            <h1><?= esc($title ?? 'Email Verification') ?></h1>
            <p class="msg"><?= esc($message ?? '') ?></p>
            <a href="<?= base_url(!empty($showLogin) ? 'login' : '') ?>" class="gradeint-cta">
                <?= !empty($showLogin) ? 'Go to Login' : 'Back to Homepage' ?>
            </a>
        </div>
    </div>
</body>
</html>
