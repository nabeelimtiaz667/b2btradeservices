<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">Edit Company/Products</h1>
    <a href="<?= base_url('dashboard/supplier') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-body">
        <form action="<?= base_url('dashboard/supplier/profile/edit') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <h5 class="mb-3 pb-2 border-bottom">Products & Services</h5>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Products / Services You Sell</label>
                    <input type="text" name="selling_products" class="form-control" value="<?= esc($supplier['selling_products'] ?? '') ?>" placeholder="e.g., Textiles, Garments, Leather Goods">
                </div>
            </div>

            <h5 class="mt-4 mb-3 pb-2 border-bottom">Company Introduction</h5>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Company Introduction</label>
                    <textarea name="company_introduction" class="form-control" rows="5" placeholder="Write a brief introduction about your company..."><?= esc($supplier['company_introduction'] ?? '') ?></textarea>
                </div>
            </div>

            <h5 class="mt-4 mb-3 pb-2 border-bottom">Images</h5>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Company Logo <small class="text-muted">(max 500 KB, JPG/PNG/WebP)</small></label>
                    <input type="file" name="company_logo" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                    <?php if (!empty($supplier['company_logo'])): ?>
                        <div class="mt-2 d-flex align-items-center gap-3">
                            <img src="<?= base_url('uploads/suppliers/' . $supplier['company_logo']) ?>" style="max-height: 60px; border-radius: 4px;" onerror="this.style.display='none'">
                            <span class="text-muted"><?= esc($supplier['company_logo']) ?></span>
                            <label class="text-danger" style="cursor: pointer; font-size: 13px;">
                                <input type="checkbox" name="remove_logo" value="1" class="me-1"> Remove
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <label class="form-label mb-2">Banner / Cover Images <small class="text-muted">(up to 3 slides, max 1 MB each, JPG/PNG/WebP)</small></label>
            <div class="row">
                <?php
                    $bannerFields = [
                        ['field' => 'banner_image',   'input' => 'banner_image',   'remove' => 'remove_banner',   'label' => 'Slide 1'],
                        ['field' => 'banner_image_2', 'input' => 'banner_image_2', 'remove' => 'remove_banner_2', 'label' => 'Slide 2'],
                        ['field' => 'banner_image_3', 'input' => 'banner_image_3', 'remove' => 'remove_banner_3', 'label' => 'Slide 3'],
                    ];
                ?>
                <?php foreach ($bannerFields as $bf): ?>
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted small"><?= $bf['label'] ?></label>
                    <input type="file" name="<?= $bf['input'] ?>" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                    <?php if (!empty($supplier[$bf['field']])): ?>
                        <div class="mt-2">
                            <img src="<?= base_url('uploads/suppliers/' . $supplier[$bf['field']]) ?>" style="max-height: 50px; border-radius: 4px;" onerror="this.style.display='none'">
                            <label class="text-danger ms-2" style="cursor: pointer; font-size: 13px;">
                                <input type="checkbox" name="<?= $bf['remove'] ?>" value="1" class="me-1"> Remove
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn" style="background: var(--primary-gradient); color: #fff;">Save Changes</button>
                <a href="<?= base_url('dashboard/supplier') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom mt-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">My Products (<?= count($products) ?>)</h5>
            <a href="<?= base_url('dashboard/supplier/products/add') ?>" class="btn btn-sm" style="background: var(--primary-gradient); color: #fff;">Add Product</a>
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
                            <a href="<?= base_url('dashboard/supplier/products/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="<?= base_url('product/detail/' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted mb-0">No products added yet. <a href="<?= base_url('dashboard/supplier/products/add') ?>">Add your first product</a>.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
