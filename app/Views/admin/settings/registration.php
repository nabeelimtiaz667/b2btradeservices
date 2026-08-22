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
    <li class="nav-item"><a class="nav-link <?= $activeTab === 'hero-banners' ? 'active' : '' ?>" href="<?= base_url('admin/settings/hero-banners') ?>">Hero Banner</a></li>
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
    <div class="card-header"><h5 class="mb-0">Registration & User Settings</h5></div>
    <div class="card-body">
        <form method="post" action="<?= base_url('admin/settings/registration') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="allow_registration" value="1" <?= ($settings['allow_registration'] ?? '1') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label">Allow Registration</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Default User Status on Registration</label>
                <select name="default_user_status" class="form-select">
                    <option value="approved" <?= ($settings['default_user_status'] ?? 'approved') === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="pending" <?= ($settings['default_user_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Max Products Per Supplier</label>
                <input type="number" name="max_products_per_supplier" class="form-control" value="<?= esc($settings['max_products_per_supplier'] ?? '0') ?>" min="0">
                <small class="text-muted">0 = unlimited</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Max Inquiries Per Buyer</label>
                <input type="number" name="max_inquiries_per_buyer" class="form-control" value="<?= esc($settings['max_inquiries_per_buyer'] ?? '0') ?>" min="0">
                <small class="text-muted">0 = unlimited</small>
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
