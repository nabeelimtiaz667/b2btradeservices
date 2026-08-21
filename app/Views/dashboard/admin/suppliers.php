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

<?php
    // Column-header sort links, same pattern as admin/settings/listings.php.
    // A closure, not a named function, to avoid any "cannot redeclare" risk
    // if this view is ever rendered more than once in a request.
    $supplierSort = $sort ?? 'created_at';
    $supplierDir = $dir ?? 'desc';
    // Every row-action form below posts here too, current sort in the query
    // string -- without this, toggling/setting-featured would reset the
    // admin's current column sort (same reasoning as listings.php).
    $suppliersActionUrl = base_url('dashboard/suppliers') . '?sort=' . urlencode($supplierSort) . '&dir=' . urlencode($supplierDir);
    $supplierSortLink = function ($field, $label) use ($supplierSort, $supplierDir) {
        $nextDir = ($supplierSort === $field && $supplierDir === 'asc') ? 'desc' : 'asc';
        $arrow = $supplierSort === $field ? ($supplierDir === 'asc' ? '&#9650;' : '&#9660;') : '<span class="text-muted">&#8597;</span>';
        $url = base_url('dashboard/suppliers') . '?sort=' . urlencode($field) . '&dir=' . urlencode($nextDir);
        return '<a href="' . esc($url, 'attr') . '" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">'
            . esc($label) . ' ' . $arrow . '</a>';
    };
?>
<div class="card card-custom">
    <div class="card-body">
        <?php if (!empty($suppliers)): ?>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th><?= $supplierSortLink('uid', 'UID') ?></th>
                        <th><?= $supplierSortLink('company_name', 'Company Name') ?></th>
                        <th><?= $supplierSortLink('email', 'Email') ?></th>
                        <th><?= $supplierSortLink('country_name', 'Country') ?></th>
                        <th><?= $supplierSortLink('membership_level', 'Membership') ?></th>
                        <th><?= $supplierSortLink('status', 'Status') ?></th>
                        <th><?= $supplierSortLink('is_featured', 'Featured') ?></th>
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
                            <div class="d-flex align-items-center gap-1">
                                <form method="post" action="<?= esc($suppliersActionUrl, 'attr') ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="toggle_featured_supplier">
                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                    <button type="submit" class="btn btn-sm <?= $s['is_featured'] ? 'btn-warning' : 'btn-outline-secondary' ?>"><?= $s['is_featured'] ? '★' : '☆' ?></button>
                                </form>
                                <?php if ($s['is_featured']): ?>
                                <form method="post" action="<?= esc($suppliersActionUrl, 'attr') ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="set_supplier_featured_set">
                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                    <select name="featured_set" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()" title="Which carousel set this supplier is pinned into (max 2 pinned suppliers per set)">
                                        <?php for ($set = 1; $set <= ($supplierSetCount ?? 1); $set++): ?>
                                        <option value="<?= $set ?>" <?= (int) ($s['featured_set'] ?? 1) === $set ? 'selected' : '' ?>>Set <?= $set ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </form>
                                <?php endif; ?>
                            </div>
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
