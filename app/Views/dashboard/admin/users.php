<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">Manage Users</h1>
</div>

<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="get" action="<?= base_url('dashboard/users') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Filter by Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Filter by Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="supplier" <?= ($filters['type'] ?? '') === 'supplier' ? 'selected' : '' ?>>Suppliers</option>
                    <option value="buyer" <?= ($filters['type'] ?? '') === 'buyer' ? 'selected' : '' ?>>Buyers</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn" style="background: var(--primary-gradient); color: #fff;">Apply Filters</button>
                <a href="<?= base_url('dashboard/users') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Users List</h5>
        <span class="text-muted">Total: <?= $total_users ?? 0 ?> users</span>
    </div>
    <div class="card-body">
        <?php if (!empty($users)): ?>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>#<?= $u['id'] ?></td>
                        <td><?= esc($u['name']) ?></td>
                        <td><?= esc($u['email']) ?></td>
                        <td><?= esc(($u['phone_code'] ?? '') . ' ' . ($u['phone'] ?? '-')) ?></td>
                        <td><span class="text-capitalize"><?= esc($u['user_type']) ?></span></td>
                        <td>
                            <?php if ($u['status'] === 'pending'): ?>
                                <span class="badge badge-pending">Pending</span>
                            <?php elseif ($u['status'] === 'approved'): ?>
                                <span class="badge badge-approved">Approved</span>
                            <?php else: ?>
                                <span class="badge badge-rejected">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?= base_url('dashboard/admin-edit-user/' . $u['id']) ?>" class="btn btn-sm btn-outline-primary" style="font-size:12px;">Edit</a>
                                <?php if ($u['status'] !== 'approved'): ?>
                                    <a href="<?= base_url('dashboard/approve/' . $u['id']) ?>" class="btn btn-approve btn-sm">Approve</a>
                                <?php endif; ?>
                                <?php if ($u['status'] !== 'rejected'): ?>
                                    <a href="<?= base_url('dashboard/reject/' . $u['id']) ?>" class="btn btn-reject btn-sm">Reject</a>
                                <?php endif; ?>
                                <a href="<?= base_url('dashboard/delete/' . $u['id']) ?>" class="btn btn-delete btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-muted text-center mb-0">No users found matching your criteria.</p>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
