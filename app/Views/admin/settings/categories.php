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

<div class="card card-custom mb-4">
    <div class="card-header"><h5 class="mb-0">Add New Category</h5></div>
    <div class="card-body">
        <form method="post" action="<?= base_url('admin/settings/categories') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Add Category</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom">
    <div class="card-header"><h5 class="mb-0">All Categories</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr id="cat-row-<?= $cat['id'] ?>">
                                <td><?= esc($cat['id']) ?></td>
                                <td><?= esc($cat['name']) ?></td>
                                <td><?= esc($cat['slug']) ?></td>
                                <td><?= esc($cat['description'] ?? '') ?></td>
                                <td><span class="badge bg-<?= $cat['status'] === 'active' ? 'success' : 'secondary' ?>"><?= esc($cat['status']) ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="showEditForm(<?= $cat['id'] ?>)">Edit</button>
                                    <form method="post" action="<?= base_url('admin/settings/categories') ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <tr id="cat-edit-<?= $cat['id'] ?>" style="display:none;">
                                <td colspan="6">
                                    <form method="post" action="<?= base_url('admin/settings/categories') ?>" class="row g-2 align-items-end">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                        <div class="col-md-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" name="name" class="form-control" value="<?= esc($cat['name']) ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Description</label>
                                            <input type="text" name="description" class="form-control" value="<?= esc($cat['description'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active" <?= $cat['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                <option value="inactive" <?= $cat['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-success btn-sm">Save</button>
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="hideEditForm(<?= $cat['id'] ?>)">Cancel</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">No categories found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function showEditForm(id) {
    document.getElementById('cat-row-' + id).style.display = 'none';
    document.getElementById('cat-edit-' + id).style.display = '';
}
function hideEditForm(id) {
    document.getElementById('cat-row-' + id).style.display = '';
    document.getElementById('cat-edit-' + id).style.display = 'none';
}
</script>
<?= $this->endSection() ?>
