<?php

use App\Models\SiteSettingModel;

if (!function_exists('sendViaPhpMail')) {
    function sendViaPhpMail(string $to, string $subject, string $htmlBody, string $fromEmail, string $fromName): bool
    {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . $fromName . " <" . $fromEmail . ">\r\n";
        $headers .= "Reply-To: " . $fromEmail . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        return @mail($to, $subject, $htmlBody, $headers);
    }
}

if (!function_exists('sendViaSmtp')) {
    function sendViaSmtp(string $to, string $subject, string $htmlBody, string $fromEmail, string $fromName, string $smtpHost, int $smtpPort, string $smtpUser, string $smtpPass): bool
    {
        $email = \Config\Services::email();
        $email->initialize([
            'protocol'   => 'smtp',
            'SMTPHost'   => $smtpHost,
            'SMTPPort'   => $smtpPort,
            'SMTPUser'   => $smtpUser,
            'SMTPPass'   => $smtpPass,
            'SMTPCrypto' => $smtpPort == 465 ? 'ssl' : 'tls',
            'mailType'   => 'html',
            'charset'    => 'UTF-8',
        ]);

        $email->setFrom($fromEmail, $fromName);
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($htmlBody);

        if ($email->send()) {
            return true;
        }

        log_message('error', 'SMTP send failed: ' . $email->printDebugger(['headers']));
        return false;
    }
}

if (!function_exists('sendAdminNotification')) {
    function sendAdminNotification(string $subject, string $message): bool
    {
        $settingModel = new SiteSettingModel();
        $adminEmail = $settingModel->getSetting('admin_notification_email', '');
        $smtpHost = $settingModel->getSetting('smtp_host', '');
        $smtpPort = (int) $settingModel->getSetting('smtp_port', '587');
        $smtpUser = $settingModel->getSetting('smtp_user', '');
        $smtpPass = $settingModel->getSetting('smtp_pass', '');
        $siteName = $settingModel->getSetting('site_name', 'B2B Trade Services');
        $contactEmail = $settingModel->getSetting('contact_email', '');

        if (empty($adminEmail)) {
            log_message('info', 'Admin notification skipped: no admin_notification_email configured.');
            return false;
        }

        $fromEmail = !empty($contactEmail) ? $contactEmail : (!empty($smtpUser) ? $smtpUser : 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

        $hasSmtp = !empty($smtpHost) && !empty($smtpUser) && !empty($smtpPass);

        if ($hasSmtp) {
            try {
                if (sendViaSmtp($adminEmail, $subject, $message, $fromEmail, $siteName, $smtpHost, $smtpPort, $smtpUser, $smtpPass)) {
                    log_message('info', 'Admin notification sent via SMTP to ' . $adminEmail);
                    return true;
                }
            } catch (\Exception $e) {
                log_message('error', 'Admin notification SMTP exception: ' . $e->getMessage());
            }
            log_message('warning', 'Admin notification SMTP failed, falling back to PHP mail().');
        } else {
            log_message('info', 'Admin notification: no SMTP configured, using PHP mail().');
        }

        if (sendViaPhpMail($adminEmail, $subject, $message, $fromEmail, $siteName)) {
            log_message('info', 'Admin notification sent via PHP mail() to ' . $adminEmail);
            return true;
        }

        log_message('error', 'Admin notification failed via both SMTP and PHP mail() for ' . $adminEmail);
        return false;
    }
}

if (!function_exists('notifyAdminNewRegistration')) {
    function notifyAdminNewRegistration(array $userData): void
    {
        $settingModel = new SiteSettingModel();
        if ($settingModel->getSetting('notify_on_registration', '0') !== '1') {
            return;
        }

        $siteName = $settingModel->getSetting('site_name', 'B2B Trade Services');
        $subject  = "[{$siteName}] New User Registration: " . ($userData['name'] ?? 'Unknown');
        $logoUrl  = 'https://securecdn.sirv.com/b2b-header-logov2.png'; //rtrim(base_url(), '/') . '/assets/images/b2b-header-logov2.png';

        $message  = "<div style='text-align:center;padding:20px 24px;background:#ffffff;border-radius:8px 8px 0 0;border-bottom:3px solid #15A2A0;margin-bottom:16px;'><img src='" . esc($logoUrl) . "' alt='" . esc($siteName) . "' style='max-height:50px;max-width:180px;'></div>";
        $message .= "<h2 style='color:#333;'>New User Registration</h2>";
        $message .= "<p><strong>Name:</strong> " . esc($userData['name'] ?? '') . "</p>";
        $message .= "<p><strong>Email:</strong> " . esc($userData['email'] ?? '') . "</p>";
        $message .= "<p><strong>Type:</strong> " . esc($userData['user_type'] ?? '') . "</p>";
        $message .= "<p><strong>Company:</strong> " . esc($userData['company_name'] ?? '') . "</p>";
        $message .= "<p><strong>Phone:</strong> " . esc(($userData['phone_code'] ?? '') . ($userData['phone'] ?? '')) . "</p>";
        $message .= "<p><strong>Status:</strong> " . esc($userData['status'] ?? '') . "</p>";
        $message .= "<p><strong>Source:</strong> " . esc($userData['lead_source'] ?? 'website') . "</p>";

        sendAdminNotification($subject, $message);
    }
}

if (!function_exists('notifyAdminNewListing')) {
    function notifyAdminNewListing(string $type, array $listingData): void
    {
        $settingModel = new SiteSettingModel();
        if ($settingModel->getSetting('notify_on_new_listing', '0') !== '1') {
            return;
        }

        $siteName = $settingModel->getSetting('site_name', 'B2B Trade Services');
        $name     = $listingData['name'] ?? $listingData['title'] ?? 'Untitled';
        $subject  = "[{$siteName}] New {$type}: {$name}";
        $logoUrl  = 'https://securecdn.sirv.com/b2b-header-logov2.png'; //rtrim(base_url(), '/') . '/assets/images/b2b-header-logov2.png';

        $message  = "<div style='text-align:center;padding:20px 24px;background:#ffffff;border-radius:8px 8px 0 0;border-bottom:3px solid #15A2A0;margin-bottom:16px;'><img src='" . esc($logoUrl) . "' alt='" . esc($siteName) . "' style='max-height:50px;max-width:180px;'></div>";
        $message .= "<h2 style='color:#333;'>New {$type} Submitted</h2>";
        $message .= "<p><strong>Title:</strong> " . esc($name) . "</p>";
        if (!empty($listingData['description'])) {
            $message .= "<p><strong>Description:</strong> " . esc(mb_substr($listingData['description'], 0, 200)) . "</p>";
        }
        $message .= "<p><strong>Status:</strong> " . esc($listingData['status'] ?? 'active') . "</p>";

        sendAdminNotification($subject, $message);
    }
}

if (!function_exists('sendPasswordResetEmail')) {
    function sendPasswordResetEmail(string $toEmail, string $toName, string $resetLink): bool
    {
        log_message('info', 'Password reset requested for ' . $toEmail . ' — link generated successfully.');

        $settingModel = new SiteSettingModel();
        $smtpHost = $settingModel->getSetting('smtp_host', '');
        $smtpPort = (int) $settingModel->getSetting('smtp_port', '587');
        $smtpUser = $settingModel->getSetting('smtp_user', '');
        $smtpPass = $settingModel->getSetting('smtp_pass', '');
        $siteName = $settingModel->getSetting('site_name', 'B2B Trade Services');
        $contactEmail = $settingModel->getSetting('contact_email', '');

        $fromEmail = !empty($contactEmail) ? $contactEmail : (!empty($smtpUser) ? $smtpUser : 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $logoUrl   = 'https://securecdn.sirv.com/b2b-header-logov2.png'; // rtrim(base_url(), '/') . '/assets/images/b2b-header-logov2.png';

        $subject = '[' . $siteName . '] Password Reset Request';

        $message  = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>";
        $message .= "<div style='text-align:center;padding:24px;background:#ffffff;border-radius:8px 8px 0 0;border-bottom:3px solid #15A2A0;margin-bottom:24px;'>";
        $message .= "<img src='" . esc($logoUrl) . "' alt='" . esc($siteName) . "' style='max-height:60px;max-width:200px;'>";
        $message .= "</div>";
        $message .= "<h2 style='color:#333;'>Password Reset Request</h2>";
        $message .= "<p>Hi <strong>" . esc($toName) . "</strong>,</p>";
        $message .= "<p>We received a request to reset the password for your <strong>" . esc($siteName) . "</strong> account.</p>";
        $message .= "<p>Click the button below to reset your password. This link is valid for <strong>1 hour</strong>.</p>";
        $message .= "<p style='margin:30px 0;'><a href='" . esc($resetLink) . "' style='background:linear-gradient(45deg,#15A2A0,#5FC86B);color:#fff;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;'>Reset My Password</a></p>";
        $message .= "<p style='color:#666;font-size:13px;'>If the button above doesn't work, copy and paste this link into your browser:</p>";
        $message .= "<p style='color:#009688;font-size:13px;word-break:break-all;'>" . esc($resetLink) . "</p>";
        $message .= "<hr style='border:none;border-top:1px solid #eee;margin:24px 0;'>";
        $message .= "<p style='color:#999;font-size:12px;'>If you did not request a password reset, please ignore this email. Your password will not change.</p>";
        $message .= "</div>";

        $hasSmtp = !empty($smtpHost) && !empty($smtpUser) && !empty($smtpPass);

        if ($hasSmtp) {
            try {
                if (sendViaSmtp($toEmail, $subject, $message, $fromEmail, $siteName, $smtpHost, $smtpPort, $smtpUser, $smtpPass)) {
                    log_message('info', 'Password reset email sent via SMTP to ' . $toEmail);
                    return true;
                }
            } catch (\Exception $e) {
                log_message('error', 'Password reset SMTP exception: ' . $e->getMessage());
            }
            log_message('warning', 'Password reset SMTP failed for ' . $toEmail . ', falling back to PHP mail().');
        } else {
            log_message('info', 'Password reset: no SMTP configured, using PHP mail().');
        }

        if (sendViaPhpMail($toEmail, $subject, $message, $fromEmail, $siteName)) {
            log_message('info', 'Password reset email sent via PHP mail() to ' . $toEmail);
            return true;
        }

        log_message('error', 'Password reset email failed via both SMTP and PHP mail() for ' . $toEmail . ' — reset link: ' . $resetLink);
        return false;
    }
}

if (!function_exists('sendWelcomeEmail')) {
    function sendWelcomeEmail(array $userData): bool
    {
        $toEmail = $userData['email'] ?? '';
        $toName  = $userData['name'] ?? 'Member';

        if (empty($toEmail)) {
            log_message('warning', 'sendWelcomeEmail: no email address provided.');
            return false;
        }

        $settingModel = new SiteSettingModel();
        $smtpHost     = $settingModel->getSetting('smtp_host', '');
        $smtpPort     = (int) $settingModel->getSetting('smtp_port', '587');
        $smtpUser     = $settingModel->getSetting('smtp_user', '');
        $smtpPass     = $settingModel->getSetting('smtp_pass', '');
        $siteName     = $settingModel->getSetting('site_name', 'B2B Trade Services');
        $contactEmail = $settingModel->getSetting('contact_email', '');
        $siteUrl      = rtrim(base_url(), '/');

        $fromEmail = !empty($contactEmail) ? $contactEmail : (!empty($smtpUser) ? $smtpUser : 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

        $isPending   = ($userData['status'] ?? '') === 'pending';
        $accountType = ucfirst($userData['user_type'] ?? 'member');
        $subject     = 'Welcome to ' . $siteName . ' — Your Account is ' . ($isPending ? 'Under Review' : 'Ready');
        $logoUrl     = 'https://securecdn.sirv.com/b2b-header-logov2.png'; // rtrim(base_url(), '/') . '/assets/images/b2b-header-logov2.png';

        $message  = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;color:#333;'>";
        $message .= "<div style='background:#ffffff;padding:24px;border-radius:8px 8px 0 0;text-align:center;border-bottom:3px solid #15A2A0;'>";
        $message .= "<img src='" . esc($logoUrl) . "' alt='" . esc($siteName) . "' style='max-height:60px;max-width:200px;display:block;margin:0 auto 14px;'>";
        $message .= "<h1 style='color:#333;margin:0;font-size:24px;'>Welcome to " . esc($siteName) . "!</h1>";
        $message .= "</div>";
        $message .= "<div style='background:#f9f9f9;padding:30px 24px;border-radius:0 0 8px 8px;border:1px solid #eee;'>";
        $message .= "<p>Hi <strong>" . esc($toName) . "</strong>,</p>";

        if ($isPending) {
            $message .= "<p>Thank you for registering as a <strong>" . esc($accountType) . "</strong> on <strong>" . esc($siteName) . "</strong>.</p>";
            $message .= "<p>Your account is currently <strong>under review</strong>. Our team will verify your details and activate your account shortly. You will receive another email once your account is approved.</p>";
        } else {
            $message .= "<p>Thank you for joining <strong>" . esc($siteName) . "</strong> as a <strong>" . esc($accountType) . "</strong>. Your account is now active and ready to use!</p>";
            $message .= "<p style='margin:30px 0;text-align:center;'>";
            $message .= "<a href='" . esc($siteUrl . '/login') . "' style='background:linear-gradient(45deg,#15A2A0,#5FC86B);color:#fff;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;'>Login to Your Account</a>";
            $message .= "</p>";
        }

        $message .= "<hr style='border:none;border-top:1px solid #eee;margin:24px 0;'>";
        $message .= "<p style='color:#555;font-size:13px;'><strong>Your registered email:</strong> " . esc($toEmail) . "</p>";
        $message .= "<p style='color:#999;font-size:12px;'>If you did not create this account, please ignore this email or contact us at <a href='mailto:" . esc($fromEmail) . "' style='color:#15A2A0;'>" . esc($fromEmail) . "</a>.</p>";
        $message .= "</div>";
        $message .= "</div>";

        $hasSmtp = !empty($smtpHost) && !empty($smtpUser) && !empty($smtpPass);

        if ($hasSmtp) {
            try {
                if (sendViaSmtp($toEmail, $subject, $message, $fromEmail, $siteName, $smtpHost, $smtpPort, $smtpUser, $smtpPass)) {
                    log_message('info', 'Welcome email sent via SMTP to ' . $toEmail);
                    return true;
                }
            } catch (\Exception $e) {
                log_message('error', 'Welcome email SMTP exception: ' . $e->getMessage());
            }
            log_message('warning', 'Welcome email SMTP failed for ' . $toEmail . ', falling back to PHP mail().');
        } else {
            log_message('info', 'Welcome email: no SMTP configured, using PHP mail().');
        }

        if (sendViaPhpMail($toEmail, $subject, $message, $fromEmail, $siteName)) {
            log_message('info', 'Welcome email sent via PHP mail() to ' . $toEmail);
            return true;
        }

        log_message('error', 'Welcome email failed via both SMTP and PHP mail() for ' . $toEmail);
        return false;
    }
}

if (!function_exists('notifyAdminNewInquiry')) {
    function notifyAdminNewInquiry(array $inquiryData): void
    {
        $settingModel = new SiteSettingModel();
        if ($settingModel->getSetting('notify_on_inquiry', '0') !== '1') {
            return;
        }

        $siteName = $settingModel->getSetting('site_name', 'B2B Trade Services');
        $title    = $inquiryData['title'] ?? $inquiryData['product_name'] ?? 'Untitled';
        $subject  = "[{$siteName}] New Buyer Inquiry: {$title}";
        $logoUrl  = 'https://securecdn.sirv.com/b2b-header-logov2.png'; //rtrim(base_url(), '/') . '/assets/images/b2b-header-logov2.png';

        $message  = "<div style='text-align:center;padding:20px 24px;background:#ffffff;border-radius:8px 8px 0 0;border-bottom:3px solid #15A2A0;margin-bottom:16px;'><img src='" . esc($logoUrl) . "' alt='" . esc($siteName) . "' style='max-height:50px;max-width:180px;'></div>";
        $message .= "<h2 style='color:#333;'>New Buyer Inquiry</h2>";
        $message .= "<p><strong>Title:</strong> " . esc($title) . "</p>";
        $message .= "<p><strong>Buyer:</strong> " . esc($inquiryData['buyer_name'] ?? '') . "</p>";
        $message .= "<p><strong>Email:</strong> " . esc($inquiryData['buyer_email'] ?? '') . "</p>";
        if (!empty($inquiryData['description'])) {
            $message .= "<p><strong>Description:</strong> " . esc(mb_substr($inquiryData['description'], 0, 200)) . "</p>";
        }
        $message .= "<p><strong>Status:</strong> " . esc($inquiryData['status'] ?? 'active') . "</p>";

        sendAdminNotification($subject, $message);
    }
}
