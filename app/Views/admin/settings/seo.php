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
    <div class="card-header"><h5 class="mb-0">SEO Settings</h5></div>
    <div class="card-body">
        <form method="post" action="<?= base_url('admin/settings/seo') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Default Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="<?= esc($settings['meta_title'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Default Meta Description</label>
                <textarea name="meta_description" class="form-control" rows="3"><?= esc($settings['meta_description'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Meta Keywords</label>
                <textarea name="meta_keywords" class="form-control" rows="2" placeholder="keyword1, keyword2, keyword3"><?= esc($settings['meta_keywords'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Google Analytics ID</label>
                <input type="text" name="google_analytics_id" class="form-control" value="<?= esc($settings['google_analytics_id'] ?? '') ?>" placeholder="UA-XXXXXXXX or G-XXXXXXXXXX">
            </div>
            <div class="mb-3">
                <label class="form-label">Google Tag Manager ID</label>
                <input type="text" name="google_tag_manager_id" class="form-control" value="<?= esc($settings['google_tag_manager_id'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>

<div class="card card-custom mt-4">
    <div class="card-header"><h5 class="mb-0">XML Sitemaps</h5></div>
    <div class="card-body">
        <p class="text-muted">
            Sitemaps rebuild from the live database automatically every 7 days.
            Use this if you've just made a change you want search engines to
            pick up sooner — a bulk import, a new category, etc. — instead of
            waiting out the week.
        </p>
        <form method="post" action="<?= base_url('admin/settings/refresh-sitemaps') ?>">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-primary">Refresh Sitemaps Now</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
