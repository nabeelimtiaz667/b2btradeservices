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
    <div class="card-header"><h5 class="mb-0">Content Moderation Settings</h5></div>
    <div class="card-body">
        <form method="post" action="<?= base_url('admin/settings/moderation') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Restricted Keywords</label>
                <textarea name="restricted_keywords" class="form-control" rows="4" placeholder="keyword1, keyword2, keyword3"><?= esc($settings['restricted_keywords'] ?? '') ?></textarea>
                <small class="text-muted">Products, inquiries, registrations and contact submissions containing these words will be blocked from submission. Separate with commas.</small>
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="auto_approve_listings" value="1" <?= ($settings['auto_approve_listings'] ?? '1') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label">Auto-Approve Listings</label>
                </div>
                <small class="text-muted">When enabled, new products and inquiries are immediately visible. When disabled, they stay inactive until admin approves them.</small>
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="require_admin_review" value="1" <?= ($settings['require_admin_review'] ?? '0') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label">Require Admin Review for New Users</label>
                </div>
                <small class="text-muted">When enabled, new user registrations will be set to "pending" status until admin approves them, regardless of the registration default status.</small>
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="profanity_filter" value="1" <?= ($settings['profanity_filter'] ?? '0') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label">Enable Profanity Filter</label>
                </div>
                <small class="text-muted">When enabled, a built-in list of inappropriate words (profanity, scam, fraud, illegal items, etc.) will also be checked on all submissions.</small>
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
