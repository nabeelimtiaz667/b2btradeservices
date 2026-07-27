<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">Manage Suppliers</h1>
    <a href="<?= base_url('dashboard/suppliers/add') ?>" class="btn" style="background: var(--primary-gradient); color: #fff;">Add Supplier</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-body">
        <?php if (!empty($suppliers)): ?>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>UID</th>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>Membership</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppliers as $s): ?>
                    <tr>
                        <td><?= esc($s['uid'] ?? '') ?></td>
                        <td><?= esc($s['company_name'] ?? $s['name']) ?></td>
                        <td><?= esc($s['email'] ?? '') ?></td>
                        <td><?= isset($s['country']['name']) ? esc($s['country']['name']) : 'N/A' ?></td>
                        <td><span class="text-capitalize"><?= esc($s['membership_level'] ?? 'free') ?></span></td>
                        <td>
                            <?php if ($s['status'] === 'approved'): ?>
                                <span class="badge badge-approved">Approved</span>
                            <?php elseif ($s['status'] === 'pending'): ?>
                                <span class="badge badge-pending">Pending</span>
                            <?php else: ?>
                                <span class="badge badge-rejected"><?= ucfirst($s['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('dashboard/suppliers/edit/' . $s['id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="<?= base_url('dashboard/suppliers/delete/' . $s['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this supplier?')">Delete</a>
                            <a href="<?= base_url('supplier/profile/' . ($s['slug'] ?? $s['id'])) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (isset($pager)): ?>
            <div class="d-flex justify-content-center mt-3">
                <?= $pager->links('supplier', 'default_full') ?>
            </div>
        <?php endif; ?>
        <?php else: ?>
        <p class="text-muted text-center mb-0">No suppliers found.</p>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
