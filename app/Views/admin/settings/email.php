<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">Site Settings</h1>
</div>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'general' ? 'active' : '' ?>" href="<?= base_url('admin/settings/general') ?>">General</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'seo' ? 'active' : '' ?>" href="<?= base_url('admin/settings/seo') ?>">SEO</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'moderation' ? 'active' : '' ?>" href="<?= base_url('admin/settings/moderation') ?>">Content Moderation</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'categories' ? 'active' : '' ?>" href="<?= base_url('admin/settings/categories') ?>">Categories</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'listings' ? 'active' : '' ?>" href="<?= base_url('admin/settings/listings') ?>">Listings</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'registration' ? 'active' : '' ?>" href="<?= base_url('admin/settings/registration') ?>">Registration</a></li>
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'email' ? 'active' : '' ?>" href="<?= base_url('admin/settings/email') ?>">Email</a></li>
</ul>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= esc(session()->getFlashdata('success')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= esc(session()->getFlashdata('error')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-header"><h5 class="mb-0">Email & Notification Settings</h5></div>
    <div class="card-body">
        <form method="post" action="<?= base_url('admin/settings/email') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Admin Notification Email</label>
                <input type="email" name="admin_notification_email" class="form-control" value="<?= esc($settings['admin_notification_email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="notify_on_registration" value="1" <?= ($settings['notify_on_registration'] ?? '0') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label">Notify on New Registration</label>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="notify_on_new_listing" value="1" <?= ($settings['notify_on_new_listing'] ?? '0') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label">Notify on New Listing</label>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="notify_on_inquiry" value="1" <?= ($settings['notify_on_inquiry'] ?? '0') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label">Notify on New Inquiry</label>
                </div>
            </div>
            <hr>
            <h6 class="mb-3">SMTP Configuration</h6>
            <div class="alert alert-warning" style="font-size:14px;">
                <strong>Important:</strong> If you are using <em>Mailtrap</em> (sandbox.smtp.mailtrap.io), emails will <strong>not</strong> be delivered to real inboxes. Mailtrap is a testing service only.
                <br><br>
                To send real emails, use one of these SMTP providers:
                <ul class="mb-0 mt-2">
                    <li><strong>Gmail</strong> — Host: <code>smtp.gmail.com</code>, Port: <code>587</code>, use an <a href="https://myaccount.google.com/apppasswords" target="_blank">App Password</a></li>
                    <li><strong>SendGrid</strong> — Host: <code>smtp.sendgrid.net</code>, Port: <code>587</code>, Username: <code>apikey</code></li>
                    <li><strong>Mailgun</strong> — Host: <code>smtp.mailgun.org</code>, Port: <code>587</code></li>
                </ul>
                <br>
                If no SMTP is configured (or SMTP fails), the system will automatically fall back to the server's built-in mail service.
            </div>
            <div class="mb-3">
                <label class="form-label">SMTP Host</label>
                <input type="text" name="smtp_host" class="form-control" value="<?= esc($settings['smtp_host'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">SMTP Port</label>
                <input type="number" name="smtp_port" class="form-control" value="<?= esc($settings['smtp_port'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">SMTP Username</label>
                <input type="text" name="smtp_user" class="form-control" value="<?= esc($settings['smtp_user'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">SMTP Password</label>
                <div class="position-relative">
                    <input type="password" name="smtp_pass" class="form-control pe-5 pwd-toggle" value="<?= esc($settings['smtp_pass'] ?? '') ?>">
                    <i class="pwd-eye" onclick="togglePwd(this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <svg style="fill: #DBDBDB" class="pwd-eye-icon" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" d="m12 4.5c-5 0-9.27 3.11-11 7.5 1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"></path>
                        </svg>
                    </i>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
