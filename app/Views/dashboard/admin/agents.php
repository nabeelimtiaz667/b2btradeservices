<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Manage Agents</h1>
    <button class="btn btn-sm" style="background: var(--primary-gradient); color: #fff; padding: 8px 20px;" data-bs-toggle="modal" data-bs-target="#addAgentModal">
        <i class="fas fa-plus me-1"></i> Add New Agent
    </button>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Agents List</h5>
        <span class="text-muted">Total: <?= count($agents ?? []) ?> agents</span>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($agents)): ?>
        <div class="table-responsive">
            <table class="table table-custom table-bordered table-hover mb-0">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Assigned Leads</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agents as $agent): ?>
                    <tr>
                        <td style="font-weight:600; color:var(--primary-teal);"><?= esc($agent['uid']) ?></td>
                        <td><?= esc($agent['name']) ?></td>
                        <td style="font-size:13px;"><?= esc($agent['email']) ?></td>
                        <td style="font-size:13px;"><?= esc(($agent['phone_code'] ?? '') . ' ' . ($agent['phone'] ?? '-')) ?></td>
                        <td><span class="badge bg-info"><?= esc($agent['department'] ?? 'General') ?></span></td>
                        <td>
                            <?php if (($agent['status'] ?? '') === 'approved'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark"><?= ucfirst($agent['status'] ?? 'pending') ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-dark"><?= $agent['lead_count'] ?? 0 ?></span>
                        </td>
                        <td style="font-size:13px;"><?= date('M d, Y', strtotime($agent['created_at'])) ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?= base_url('dashboard/agents/edit/' . $agent['id']) ?>" class="btn btn-sm btn-outline-primary" style="font-size:11px; padding:3px 8px;">Edit</a>
                                <a href="<?= base_url('dashboard/agents/delete/' . $agent['id']) ?>" class="btn btn-sm btn-outline-danger" style="font-size:11px; padding:3px 8px;" onclick="return confirm('Are you sure you want to delete this agent?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center p-5">
            <p class="text-muted mb-0">No agents found. Add your first agent using the button above.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Agent Modal -->
<div class="modal fade" id="addAgentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('dashboard/agents/add') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header" style="background:var(--primary-dark); color:#fff;">
                    <h5 class="modal-title">Add New Agent</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Employee ID</label>
                        <input type="text" class="form-control" value="<?= esc($next_agent_id ?? 'A-000001') ?>" readonly style="background:#f8f9fa;">
                        <small class="text-muted">Auto-generated</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="Enter agent's full name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" required placeholder="agent@company.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" required minlength="6" placeholder="Minimum 6 characters">
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label class="form-label fw-bold">Phone Code</label>
                            <input type="text" class="form-control" name="phone_code" placeholder="+1">
                        </div>
                        <div class="col-8">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" class="form-control" name="phone" placeholder="Phone number">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                        <select class="form-select" name="department" required>
                            <option value="">Select Department</option>
                            <option value="Sales">Sales</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Support">Support</option>
                            <option value="Operations">Operations</option>
                            <option value="Business Development">Business Development</option>
                            <option value="Account Management">Account Management</option>
                            <option value="General">General</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background:var(--primary-gradient); color:#fff;">Create Agent</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Agent Modal (for inline editing) -->
<?php if (!empty($edit_agent)): ?>
<div class="modal fade show" id="editAgentModal" tabindex="-1" style="display:block;" aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('dashboard/agents/edit/' . $edit_agent['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header" style="background:var(--primary-dark); color:#fff;">
                    <h5 class="modal-title">Edit Agent - <?= esc($edit_agent['uid']) ?></h5>
                    <a href="<?= base_url('dashboard/agents') ?>" class="btn-close btn-close-white"></a>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Employee ID</label>
                        <input type="text" class="form-control" value="<?= esc($edit_agent['uid']) ?>" readonly style="background:#f8f9fa;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="<?= esc($edit_agent['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="<?= esc($edit_agent['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">New Password</label>
                        <input type="password" class="form-control" name="password" minlength="6" placeholder="Leave blank to keep current">
                        <small class="text-muted">Leave empty if you don't want to change the password</small>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label class="form-label fw-bold">Phone Code</label>
                            <input type="text" class="form-control" name="phone_code" value="<?= esc($edit_agent['phone_code'] ?? '') ?>">
                        </div>
                        <div class="col-8">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="<?= esc($edit_agent['phone'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                        <select class="form-select" name="department" required>
                            <option value="">Select Department</option>
                            <?php foreach (['Sales', 'Marketing', 'Support', 'Operations', 'Business Development', 'Account Management', 'General'] as $dept): ?>
                            <option value="<?= $dept ?>" <?= ($edit_agent['department'] ?? '') === $dept ? 'selected' : '' ?>><?= $dept ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status">
                            <option value="approved" <?= ($edit_agent['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Active</option>
                            <option value="pending" <?= ($edit_agent['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="rejected" <?= ($edit_agent['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Disabled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="<?= base_url('dashboard/agents') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn" style="background:var(--primary-gradient); color:#fff;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?= $this->endSection() ?>
