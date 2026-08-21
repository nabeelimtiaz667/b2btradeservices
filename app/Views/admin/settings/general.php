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
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'top-sections' ? 'active' : '' ?>" href="<?= base_url('admin/settings/top-sections') ?>">Top Sections</a></li>
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
    <div class="card-header"><h5 class="mb-0">General Settings</h5></div>
    <div class="card-body">
        <form method="post" action="<?= base_url('admin/settings/general') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Site Name</label>
                <input type="text" name="site_name" class="form-control" value="<?= esc($settings['site_name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Site Tagline</label>
                <input type="text" name="site_tagline" class="form-control" value="<?= esc($settings['site_tagline'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Email</label>
                <input type="email" name="contact_email" class="form-control" value="<?= esc($settings['contact_email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Phone</label>
                <input type="text" name="contact_phone" class="form-control" value="<?= esc($settings['contact_phone'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Address</label>
                <textarea name="contact_address" class="form-control" rows="3"><?= esc($settings['contact_address'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Footer Text</label>
                <input type="text" name="footer_text" class="form-control" value="<?= esc($settings['footer_text'] ?? '') ?>">
            </div>

            <hr class="my-4">

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label">Maintenance Mode</label>
                </div>
                <small class="form-text text-muted">When enabled, only admins can access the site. Visitors will see a maintenance page.</small>
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
