<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">Form Submissions</h1>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="new" <?= ($filters['status'] ?? '') === 'new' ? 'selected' : '' ?>>New</option>
                    <option value="read" <?= ($filters['status'] ?? '') === 'read' ? 'selected' : '' ?>>Read</option>
                    <option value="replied" <?= ($filters['status'] ?? '') === 'replied' ? 'selected' : '' ?>>Replied</option>
                    <option value="closed" <?= ($filters['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Form Type</label>
                <select name="form_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="contact" <?= ($filters['form_type'] ?? '') === 'contact' ? 'selected' : '' ?>>Contact</option>
                    <option value="quote" <?= ($filters['form_type'] ?? '') === 'quote' ? 'selected' : '' ?>>Quote Request</option>
                    <option value="supplier_inquiry" <?= ($filters['form_type'] ?? '') === 'supplier_inquiry' ? 'selected' : '' ?>>Supplier Inquiry</option>
                    <option value="product_quote" <?= ($filters['form_type'] ?? '') === 'product_quote' ? 'selected' : '' ?>>Product Quote</option>
                    <option value="buyer_inquiry" <?= ($filters['form_type'] ?? '') === 'buyer_inquiry' ? 'selected' : '' ?>>Buyer Inquiry</option>
                    <option value="package_inquiry" <?= ($filters['form_type'] ?? '') === 'package_inquiry' ? 'selected' : '' ?>>Package Inquiry</option>
                    <option value="tradeshow_application" <?= ($filters['form_type'] ?? '') === 'tradeshow_application' ? 'selected' : '' ?>>Tradeshow Application</option>
                    <option value="agent_partner_application" <?= ($filters['form_type'] ?? '') === 'agent_partner_application' ? 'selected' : '' ?>>Agent Partner Application</option>
                    <option value="partner_inquiry" <?= ($filters['form_type'] ?? '') === 'partner_inquiry' ? 'selected' : '' ?>>Partner Inquiry</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="<?= base_url('dashboard/submissions') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($submissions)): ?>
                        <?php foreach ($submissions as $s): ?>
                            <tr class="<?= $s['status'] === 'new' ? 'table-warning' : '' ?>">
                                <td><?= $s['id'] ?></td>
                                <td><span class="badge bg-info"><?= esc(ucwords(str_replace('_', ' ', $s['form_type']))) ?></span></td>
                                <td><?= esc($s['name']) ?></td>
                                <td><?= esc($s['email']) ?></td>
                                <td><?= esc($s['source_page'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $statusColors = ['new' => 'warning', 'read' => 'info', 'replied' => 'success', 'closed' => 'secondary'];
                                    $color = $statusColors[$s['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $color ?>"><?= ucfirst($s['status']) ?></span>
                                </td>
                                <td><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                                <td>
                                    <a href="<?= base_url('dashboard/submissions/view/' . $s['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="<?= base_url('dashboard/submissions/delete/' . $s['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No submissions found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
