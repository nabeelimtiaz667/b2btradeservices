<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">Edit User Profile</h1>
        <p class="text-muted mb-0"><?= esc($target_user['uid']) ?> - <?= ucfirst(esc($target_user['user_type'])) ?></p>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-<?= $target_user['user_type'] === 'supplier' ? 'success' : ($target_user['user_type'] === 'buyer' ? 'primary' : 'info') ?> fs-6 px-3 py-2"><?= ucfirst(esc($target_user['user_type'])) ?></span>
        <a href="<?= base_url('dashboard/users') ?>" class="btn btn-outline-secondary btn-sm">Back to Users</a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="profile-card">
            <div class="profile-avatar">
                <?= strtoupper(substr($target_user['name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="profile-name"><?= esc($target_user['name']) ?></div>
            <div class="profile-email"><?= esc($target_user['email']) ?></div>
            <ul class="profile-info-list">
                <li><span class="label">UID</span><span class="value"><?= esc($target_user['uid']) ?></span></li>
                <li><span class="label">Type</span><span class="value"><?= ucfirst(esc($target_user['user_type'])) ?></span></li>
                <li><span class="label">Status</span><span class="value"><span class="badge badge-<?= $target_user['status'] === 'approved' ? 'approved' : ($target_user['status'] === 'pending' ? 'pending' : 'rejected') ?>"><?= ucfirst(esc($target_user['status'])) ?></span></span></li>
                <li><span class="label">Membership</span><span class="value"><?= ucfirst(esc($target_user['membership_level'] ?? 'Free')) ?></span></li>
                <li><span class="label">Registered</span><span class="value"><?= date('M d, Y', strtotime($target_user['created_at'])) ?></span></li>
                <?php if (!empty($target_user['last_login_at'])): ?>
                <li><span class="label">Last Login</span><span class="value"><?= date('M d, Y H:i', strtotime($target_user['last_login_at'])) ?></span></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header"><h5>Edit Profile</h5></div>
            <div class="card-body">
                <form action="<?= base_url('dashboard/admin-edit-user/' . $target_user['id']) ?>" method="POST">
                    <?= csrf_field() ?>

                    <h6 class="mb-3" style="color: var(--primary-dark); font-weight: 600;">Personal Information</h6>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="<?= esc($target_user['name']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" value="<?= esc($target_user['email']) ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Phone Code</label>
                            <input type="text" class="form-control" name="phone_code" value="<?= esc($target_user['phone_code'] ?? '') ?>">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="<?= esc($target_user['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end pb-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="whatsapp" value="1" <?= !empty($target_user['whatsapp']) ? 'checked' : '' ?>>
                                <label class="form-check-label">WhatsApp</label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3" style="color: var(--primary-dark); font-weight: 600;">Account Settings (Admin Only)</h6>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="approved" <?= ($target_user['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="pending" <?= ($target_user['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="rejected" <?= ($target_user['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Membership Level</label>
                            <select class="form-select" name="membership_level">
                                <?php foreach ($membership_levels as $key => $label): ?>
                                <option value="<?= $key ?>" <?= ($target_user['membership_level'] ?? 'free') === $key ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Lead Stage</label>
                            <select class="form-select" name="lead_stage">
                                <?php foreach ($lead_stages as $key => $label): ?>
                                <option value="<?= $key ?>" <?= ($target_user['lead_stage'] ?? 'new') === $key ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assigned Agent</label>
                            <select class="form-select" name="assigned_agent_id">
                                <option value="">Unassigned</option>
                                <?php foreach ($agents as $agent): ?>
                                <option value="<?= $agent['id'] ?>" <?= ($target_user['assigned_agent_id'] ?? '') == $agent['id'] ? 'selected' : '' ?>><?= esc($agent['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lead Source</label>
                            <input type="text" class="form-control" name="lead_source" value="<?= esc($target_user['lead_source'] ?? '') ?>">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3" style="color: var(--primary-dark); font-weight: 600;">Business Information</h6>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" name="company_name" value="<?= esc($target_user['company_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Website</label>
                            <input type="url" class="form-control" name="website" value="<?= esc($target_user['website'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <select class="form-control" name="country_id">
                                <option value="">Select Country</option>
                                <?php foreach ($countries as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($target_user['country_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" value="<?= esc($target_user['city'] ?? '') ?>">
                        </div>
                    </div>

                    <?php if ($target_user['user_type'] === 'supplier'): ?>
                    <div class="mb-3">
                        <label class="form-label">Products They Sell</label>
                        <textarea class="form-control" name="selling_products" rows="3"><?= esc($target_user['selling_products'] ?? '') ?></textarea>
                    </div>
                    <?php endif; ?>

                    <?php if ($target_user['user_type'] === 'buyer'): ?>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Products They Buy</label>
                            <textarea class="form-control" name="buying_products" rows="3"><?= esc($target_user['buying_products'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Requirements</label>
                            <textarea class="form-control" name="requirement" rows="3"><?= esc($target_user['requirement'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($target_user['user_type'] === 'agent'): ?>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select class="form-select" name="department">
                            <option value="">Select Department</option>
                            <?php foreach (['Sales', 'Marketing', 'Support', 'Operations', 'Business Development', 'Account Management', 'General'] as $dept): ?>
                            <option value="<?= $dept ?>" <?= ($target_user['department'] ?? '') === $dept ? 'selected' : '' ?>><?= $dept ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <hr class="my-4">
                    <h6 class="mb-3" style="color: var(--primary-dark); font-weight: 600;">Change Password</h6>
                    <p class="text-muted small mb-3">Leave blank if you don't want to change the password.</p>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" minlength="6" placeholder="Enter new password">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-approve px-4 py-2">Save Changes</button>
                        <a href="<?= base_url('dashboard/users') ?>" class="btn btn-outline-secondary px-4 py-2 ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
