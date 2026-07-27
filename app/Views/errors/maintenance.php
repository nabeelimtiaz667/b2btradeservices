<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($siteSettings['site_name'] ?? 'B2B Trade Services') ?> - Maintenance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #0A504F 0%, #0F9EA5 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; color: #fff; }
        .container { text-align: center; max-width: 600px; padding: 40px 20px; }
        .icon { font-size: 80px; margin-bottom: 20px; }
        h1 { font-size: 36px; margin-bottom: 15px; }
        p { font-size: 18px; opacity: 0.9; line-height: 1.6; }
        .contact { margin-top: 30px; font-size: 14px; opacity: 0.7; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">&#9881;</div>
        <h1>Under Maintenance</h1>
        <p>We are currently performing scheduled maintenance to improve your experience. We'll be back shortly.</p>
        <?php if (!empty($siteSettings['contact_email'])): ?>
        <p class="contact">For urgent inquiries, please contact: <?= esc($siteSettings['contact_email']) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
