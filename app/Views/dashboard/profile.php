<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">My Profile</h1>
        <p class="text-muted mb-0">View and update your account information</p>
    </div>
    <div>
        <span class="badge bg-<?= $user['user_type'] === 'admin' ? 'danger' : ($user['user_type'] === 'supplier' ? 'primary' : ($user['user_type'] === 'agent' ? 'info' : 'success')) ?> fs-6 px-3 py-2"><?= ucfirst(esc($user['user_type'])) ?></span>
    </div>
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

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="profile-card">
            <div class="profile-avatar">
                <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="profile-name"><?= esc($user['name']) ?></div>
            <div class="profile-email"><?= esc($user['email']) ?></div>
            <ul class="profile-info-list">
                <li>
                    <span class="label">UID</span>
                    <span class="value"><?= esc($user['uid'] ?? 'N/A') ?></span>
                </li>
                <li>
                    <span class="label">Account Type</span>
                    <span class="value"><?= ucfirst(esc($user['user_type'])) ?></span>
                </li>
                <li>
                    <span class="label">Status</span>
                    <span class="value">
                        <span class="badge badge-<?= $user['status'] === 'approved' ? 'approved' : ($user['status'] === 'pending' ? 'pending' : 'rejected') ?>"><?= ucfirst(esc($user['status'])) ?></span>
                    </span>
                </li>
                <li>
                    <span class="label">Membership</span>
                    <span class="value"><?= ucfirst(esc($user['membership_level'] ?? 'Free')) ?></span>
                </li>
                <li>
                    <span class="label">Member Since</span>
                    <span class="value"><?= date('M d, Y', strtotime($user['created_at'])) ?></span>
                </li>
                <?php if (!empty($user['last_login_at'])): ?>
                <li>
                    <span class="label">Last Login</span>
                    <span class="value"><?= date('M d, Y H:i', strtotime($user['last_login_at'])) ?></span>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header">
                <h5>Update Profile</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('dashboard/profile/update') ?>" method="POST">
                    <?= csrf_field() ?>

                    <h6 class="mb-3" style="color: var(--primary-dark); font-weight: 600;">Personal Information</h6>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="<?= esc($user['name']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" value="<?= esc($user['email']) ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Phone Code</label>
                            <input type="text" class="form-control" name="phone_code" value="<?= esc($user['phone_code'] ?? '') ?>" placeholder="+1">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="<?= esc($user['phone'] ?? '') ?>" placeholder="Phone number">
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end pb-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="whatsapp" id="whatsappCheck" value="1" <?= !empty($user['whatsapp']) && $user['whatsapp'] == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="whatsappCheck">This number is on WhatsApp</label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3" style="color: var(--primary-dark); font-weight: 600;">Business Information</h6>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" name="company_name" value="<?= esc($user['company_name'] ?? '') ?>" placeholder="Your company name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Website</label>
                            <input type="url" class="form-control" name="website" value="<?= esc($user['website'] ?? '') ?>" placeholder="https://example.com">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <select class="form-control" name="country_id">
                                <option value="">Select Country</option>
                                <?php if (isset($countries)): ?>
                                    <?php foreach ($countries as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= ($user['country_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" value="<?= esc($user['city'] ?? '') ?>" placeholder="Your city">
                        </div>
                    </div>

                    <?php if ($user['user_type'] === 'supplier'): ?>
                    <div class="row mb-3">
                        <div class="col-12 mb-3">
                            <label class="form-label">Products You Sell</label>
                            <textarea class="form-control" name="selling_products" rows="3" placeholder="List the products you sell (e.g., Textiles, Electronics, Machinery)"><?= esc($user['selling_products'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($user['user_type'] === 'buyer'): ?>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Products You Buy</label>
                            <textarea class="form-control" name="buying_products" rows="3" placeholder="List the products you're looking for"><?= esc($user['buying_products'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Specific Requirements</label>
                            <textarea class="form-control" name="requirement" rows="3" placeholder="Describe your specific requirements"><?= esc($user['requirement'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <?php endif; ?>

                    <hr class="my-4">
                    <h6 class="mb-3" style="color: var(--primary-dark); font-weight: 600;">Change Password</h6>
                    <p class="text-muted small mb-3">Leave blank if you don't want to change your password.</p>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control pe-5 pwd-toggle" name="new_password" placeholder="Enter new password" minlength="6">
                                <i class="pwd-eye" onclick="togglePwd(this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                    <svg style="fill: #DBDBDB" class="pwd-eye-icon" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                                        <path clip-rule="evenodd" d="m12 4.5c-5 0-9.27 3.11-11 7.5 1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"></path>
                                    </svg>
                                </i>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control pe-5 pwd-toggle" name="confirm_password" placeholder="Confirm new password">
                                <i class="pwd-eye" onclick="togglePwd(this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                    <svg style="fill: #DBDBDB" class="pwd-eye-icon" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                                        <path clip-rule="evenodd" d="m12 4.5c-5 0-9.27 3.11-11 7.5 1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"></path>
                                    </svg>
                                </i>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-approve px-4 py-2" style="font-size: 15px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                            </svg>
                            Save Changes
                        </button>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary px-4 py-2 ms-2" style="font-size: 15px;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
