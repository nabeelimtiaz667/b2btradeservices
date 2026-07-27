<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">My Inquiries</h1>
        <p class="text-muted mb-0">Manage your product inquiries</p>
    </div>
    <a href="<?= base_url('dashboard/buyer/inquiries/add') ?>" class="btn btn-success px-4 py-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Post New Inquiry
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

<div class="card card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Inquiries (<?= count($inquiries) ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($inquiries)): ?>
            <div class="text-center py-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ccc" viewBox="0 0 16 16">
                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                </svg>
                <h5 class="mt-3 text-muted">No inquiries yet</h5>
                <p class="text-muted">Start by posting your first product inquiry.</p>
                <a href="<?= base_url('dashboard/buyer/inquiries/add') ?>" class="btn btn-success">Post Your First Inquiry</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Target Price</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inquiries as $inquiry): ?>
                        <tr>
                            <td><strong><?= esc($inquiry['title']) ?></strong></td>
                            <td><?= esc($inquiry['product_name'] ?? 'N/A') ?></td>
                            <td><?= esc($inquiry['category']['name'] ?? 'N/A') ?></td>
                            <td><?= esc(($inquiry['quantity'] ?? '') . ($inquiry['unit'] ? ' ' . $inquiry['unit'] : '')) ?></td>
                            <td><?= esc($inquiry['target_price'] ?? 'N/A') ?></td>
                            <td>
                                <?php if ($inquiry['status'] === 'active'): ?>
                                    <span class="badge badge-approved">Active</span>
                                <?php elseif ($inquiry['status'] === 'closed'): ?>
                                    <span class="badge badge-rejected">Closed</span>
                                <?php else: ?>
                                    <span class="badge badge-pending"><?= esc(ucfirst($inquiry['status'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M d, Y', strtotime($inquiry['created_at'])) ?></td>
                            <td>
                                <a href="<?= base_url('dashboard/buyer/inquiries/edit/' . $inquiry['id']) ?>" class="btn btn-sm btn-approve me-1">Edit</a>
                                <a href="<?= base_url('dashboard/buyer/inquiries/delete/' . $inquiry['id']) ?>" class="btn btn-sm btn-reject" onclick="return confirm('Are you sure you want to delete this inquiry?')">Delete</a>
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
