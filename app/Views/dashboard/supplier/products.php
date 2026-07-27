<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">My Products</h1>
        <p class="text-muted mb-0">Manage your product listings</p>
    </div>
    <a href="<?= base_url('dashboard/supplier/products/add') ?>" class="btn btn-success px-4 py-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Add Product
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php
$autoApproveOff = ($autoApproveListings ?? '1') !== '1';
$pendingCount = 0;
if (!empty($products)) {
    foreach ($products as $p) {
        if ($p['status'] === 'pending' || ($autoApproveOff && $p['status'] === 'inactive')) {
            $pendingCount++;
        }
    }
}
?>

<?php if ($pendingCount > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="flex-shrink-0">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
    </svg>
    <div>
        <strong><?= $pendingCount ?> product<?= $pendingCount > 1 ? 's' : '' ?> awaiting admin approval.</strong>
        These products are not yet visible on the site. The admin will review and activate them shortly.
    </div>
</div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Products (<?= count($products) ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ccc" viewBox="0 0 16 16">
                    <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                </svg>
                <h5 class="mt-3 text-muted">No products yet</h5>
                <p class="text-muted">Start by adding your first product.</p>
                <a href="<?= base_url('dashboard/supplier/products/add') ?>" class="btn btn-success">Add Your First Product</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price Range</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <?php if (!empty($product['main_image'])): ?>
                                    <img src="<?= base_url('uploads/products/' . $product['main_image']) ?>" alt="<?= esc($product['name']) ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                    <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ccc" viewBox="0 0 16 16">
                                            <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                            <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= esc($product['name']) ?></strong></td>
                            <td><?= esc($product['category']['name'] ?? 'N/A') ?></td>
                            <td><?= esc($product['price_range'] ?? 'N/A') ?></td>
                            <?php $isPending = $product['status'] === 'pending' || ($autoApproveOff && $product['status'] === 'inactive'); ?>
                            <td>
                                <?php if ($product['status'] === 'active'): ?>
                                    <span class="badge badge-approved">Active</span>
                                <?php elseif ($isPending): ?>
                                    <span class="badge" style="background-color:#f59e0b;color:#fff;">Pending Review</span>
                                <?php else: ?>
                                    <span class="badge badge-rejected">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('dashboard/supplier/products/edit/' . $product['id']) ?>" class="btn btn-sm btn-approve me-1">Edit</a>
                                <form action="<?= base_url('dashboard/supplier/products/toggle-status/' . $product['id']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <?php if ($product['status'] === 'active'): ?>
                                        <button type="submit" class="btn btn-sm btn-warning me-1" onclick="return confirm('Delist this product? It will be hidden from the site.')">Delist</button>
                                    <?php elseif ($isPending): ?>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-1" disabled title="Awaiting admin approval">Awaiting Approval</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-sm btn-success me-1" onclick="return confirm('Relist this product? It will become visible on the site.')">Relist</button>
                                    <?php endif; ?>
                                </form>
                                <form action="<?= base_url('dashboard/supplier/products/delete/' . $product['id']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-reject" onclick="return confirm('Are you sure you want to permanently delete this product? This cannot be undone.')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
