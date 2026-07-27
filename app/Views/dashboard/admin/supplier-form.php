<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title"><?= isset($supplier) && $supplier ? 'Edit Supplier / Buyer' : 'Add Supplier / Buyer' ?></h1>
    <a href="<?= base_url('dashboard/suppliers') ?>" class="btn btn-outline-secondary">Back to Suppliers</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-body">
        <form action="<?= isset($supplier) && $supplier ? base_url('dashboard/suppliers/edit/' . $supplier['id']) : base_url('dashboard/suppliers/add') ?>" method="post" enctype="multipart/form-data">

            <h5 class="mb-3 pb-2 border-bottom">Basic Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Company Name *</label>
                    <input type="text" name="company_name" class="form-control" value="<?= isset($supplier) ? esc($supplier['company_name'] ?? '') : old('company_name') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contact Person Name *</label>
                    <input type="text" name="name" class="form-control" value="<?= isset($supplier) ? esc($supplier['name'] ?? '') : old('name') ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" value="<?= isset($supplier) ? esc($supplier['email'] ?? '') : old('email') ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">User Type *</label>
                    <select name="user_type" class="form-control" required>
                        <option value="supplier" <?= (isset($supplier) && ($supplier['user_type'] ?? '') == 'supplier') ? 'selected' : '' ?>>Supplier</option>
                        <option value="buyer" <?= (isset($supplier) && ($supplier['user_type'] ?? '') == 'buyer') ? 'selected' : '' ?>>Buyer</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="approved" <?= (isset($supplier) && ($supplier['status'] ?? '') == 'approved') ? 'selected' : '' ?>>Approved</option>
                        <option value="pending" <?= (isset($supplier) && ($supplier['status'] ?? '') == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="rejected" <?= (isset($supplier) && ($supplier['status'] ?? '') == 'rejected') ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Country *</label>
                    <select name="country_id" class="form-control" required>
                        <option value="">Select Country</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?= $country['id'] ?>" <?= (isset($supplier) && $supplier['country_id'] == $country['id']) ? 'selected' : '' ?>><?= esc($country['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="<?= isset($supplier) ? esc($supplier['city'] ?? '') : old('city') ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Membership Level</label>
                    <select name="membership_level" class="form-control">
                        <option value="free" <?= (isset($supplier) && ($supplier['membership_level'] ?? '') == 'free') ? 'selected' : '' ?>>Free</option>
                        <option value="silver" <?= (isset($supplier) && ($supplier['membership_level'] ?? '') == 'silver') ? 'selected' : '' ?>>Silver</option>
                        <option value="gold" <?= (isset($supplier) && ($supplier['membership_level'] ?? '') == 'gold') ? 'selected' : '' ?>>Gold</option>
                        <option value="platinum" <?= (isset($supplier) && ($supplier['membership_level'] ?? '') == 'platinum') ? 'selected' : '' ?>>Platinum</option>
                        <option value="vip" <?= (isset($supplier) && ($supplier['membership_level'] ?? '') == 'vip') ? 'selected' : '' ?>>VIP</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= isset($supplier) ? esc($supplier['phone'] ?? '') : old('phone') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" class="form-control" value="<?= isset($supplier) ? esc($supplier['website'] ?? '') : old('website') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Products / Services</label>
                    <input type="text" name="selling_products" class="form-control" value="<?= isset($supplier) ? esc($supplier['selling_products'] ?? '') : old('selling_products') ?>" placeholder="e.g., Textiles, Garments, Leather Goods">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured" <?= (isset($supplier) && !empty($supplier['is_featured'])) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isFeatured">Featured Supplier <small class="text-muted">(Show in "Top Suppliers" on homepage)</small></label>
                    </div>
                </div>
            </div>

            <h5 class="mt-4 mb-3 pb-2 border-bottom">Company Introduction</h5>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Company Introduction</label>
                    <textarea name="company_introduction" class="form-control" rows="5" placeholder="Write a brief introduction about the company..."><?= isset($supplier) ? esc($supplier['company_introduction'] ?? '') : old('company_introduction') ?></textarea>
                </div>
            </div>

            <h5 class="mt-4 mb-3 pb-2 border-bottom">Images</h5>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Company Logo <small class="text-muted">(max 500 KB, JPG/PNG/WebP)</small></label>
                    <input type="file" name="company_logo" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                    <?php if (isset($supplier) && !empty($supplier['company_logo'])): ?>
                        <div class="mt-2 d-flex align-items-center gap-3">
                            <img src="<?= base_url('uploads/suppliers/' . $supplier['company_logo']) ?>" style="max-height: 60px; border-radius: 4px;" onerror="this.style.display='none'">
                            <span class="text-muted"><?= esc($supplier['company_logo']) ?></span>
                            <label class="text-danger" style="cursor: pointer; font-size: 13px;">
                                <input type="checkbox" name="remove_logo" value="1" class="me-1">Remove
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <label class="form-label mb-2">Banner / Cover Images <small class="text-muted">(up to 3 slides, max 1 MB each, JPG/PNG/WebP)</small></label>
            <div class="row">
                <?php
                    $bannerFields = [
                        ['field' => 'banner_image', 'input' => 'banner_image', 'remove' => 'remove_banner', 'label' => 'Slide 1'],
                        ['field' => 'banner_image_2', 'input' => 'banner_image_2', 'remove' => 'remove_banner_2', 'label' => 'Slide 2'],
                        ['field' => 'banner_image_3', 'input' => 'banner_image_3', 'remove' => 'remove_banner_3', 'label' => 'Slide 3'],
                    ];
                ?>
                <?php foreach ($bannerFields as $bf): ?>
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted small"><?= $bf['label'] ?></label>
                    <input type="file" name="<?= $bf['input'] ?>" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                    <?php if (isset($supplier) && !empty($supplier[$bf['field']])): ?>
                        <div class="mt-2">
                            <img src="<?= base_url('uploads/suppliers/' . $supplier[$bf['field']]) ?>" style="max-height: 50px; border-radius: 4px;" onerror="this.style.display='none'">
                            <label class="text-danger ms-2" style="cursor: pointer; font-size: 13px;">
                                <input type="checkbox" name="<?= $bf['remove'] ?>" value="1" class="me-1">Remove
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn" style="background: var(--primary-gradient); color: #fff;"><?= isset($supplier) && $supplier ? 'Update' : 'Add Supplier / Buyer' ?></button>
                <a href="<?= base_url('dashboard/suppliers') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php if (isset($supplier) && $supplier): ?>
<div class="card card-custom mt-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Products (<?= isset($products) ? count($products) : 0 ?>)</h5>
            <a href="<?= base_url('dashboard/suppliers/' . $supplier['id'] . '/add-product') ?>" class="btn btn-sm" style="background: var(--primary-gradient); color: #fff;">Add Product</a>
        </div>
        <?php if (!empty($products)): ?>
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td style="width: 60px;">
                            <?php if (!empty($p['main_image'])): ?>
                                <img src="<?= base_url('uploads/products/' . $p['main_image']) ?>" style="height: 40px; width: 40px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($p['name']) ?></td>
                        <td>
                            <?php if ($p['status'] === 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= ucfirst($p['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d M Y', strtotime($p['created_at'])) ?></td>
                        <td>
                            <a href="<?= base_url('dashboard/suppliers/' . $supplier['id'] . '/edit-product/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="<?= base_url('product/detail/' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted mb-0">No products added yet.</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
