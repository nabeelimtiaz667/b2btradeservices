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
    <div class="card-header"><h5 class="mb-0">Top Products / Top Suppliers Carousel</h5></div>
    <div class="card-body">
        <p class="text-muted">
            Controls how many rotating sets the homepage's "Top Products" and "Top Suppliers"
            sections cycle through, and how long each set stays on screen. Any slots not filled
            by an admin-pinned item are filled automatically by ranking -- to choose
            <em>which</em> product/supplier is pinned into <em>which</em> set, use
            the "Featured" column on the <a href="<?= base_url('admin/settings/listings') ?>">Listings</a> tab
            for products, or the "Featured Supplier" option on a supplier's edit page. A set
            can hold at most 3 pinned products or 2 pinned suppliers (its full display size) --
            assigning more than that to the same set is rejected.
        </p>
        <form method="post" action="<?= base_url('admin/settings/top-sections') ?>">
            <?= csrf_field() ?>

            <h6 class="mt-2 mb-3">Top Products</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Number of Sets</label>
                    <input type="number" name="top_products_set_count" class="form-control" min="1" max="10"
                        value="<?= esc($settings['top_products_set_count'] ?? '1') ?>">
                    <small class="form-text text-muted">1-10. Each set displays 3 products.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Seconds per Set</label>
                    <input type="number" name="top_products_interval_seconds" class="form-control" min="2" max="60"
                        value="<?= esc($settings['top_products_interval_seconds'] ?? '5') ?>">
                    <small class="form-text text-muted">2-60. How long each set shows before rotating to the next.</small>
                </div>
            </div>

            <hr class="my-4">

            <h6 class="mb-3">Top Suppliers</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Number of Sets</label>
                    <input type="number" name="top_suppliers_set_count" class="form-control" min="1" max="10"
                        value="<?= esc($settings['top_suppliers_set_count'] ?? '1') ?>">
                    <small class="form-text text-muted">1-10. Each set displays 2 suppliers.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Seconds per Set</label>
                    <input type="number" name="top_suppliers_interval_seconds" class="form-control" min="2" max="60"
                        value="<?= esc($settings['top_suppliers_interval_seconds'] ?? '5') ?>">
                    <small class="form-text text-muted">2-60. How long each set shows before rotating to the next.</small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
