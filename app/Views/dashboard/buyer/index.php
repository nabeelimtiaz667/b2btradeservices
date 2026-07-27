<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Welcome, <?= esc($user['name']) ?>!</h1>
        <p class="text-muted mb-0">Buyer Dashboard</p>
    </div>
    <div>
        <?php if ($user['status'] === 'pending'): ?>
            <span class="badge badge-pending fs-6 px-3 py-2">Pending Approval</span>
        <?php elseif ($user['status'] === 'approved'): ?>
            <span class="badge badge-approved fs-6 px-3 py-2">Approved</span>
        <?php else: ?>
            <span class="badge badge-rejected fs-6 px-3 py-2">Rejected</span>
        <?php endif; ?>
    </div>
</div>

<?php if ($user['status'] === 'pending'): ?>
<div class="pending-notice">
    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
    </svg>
    <h4>Your Account is Pending Approval</h4>
    <p>Our team is reviewing your registration. You will be notified once your account has been approved.</p>
</div>
<?php else: ?>
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="profile-card">
            <div class="profile-avatar">
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
            <h3 class="profile-name"><?= esc($user['name']) ?></h3>
            <p class="profile-email"><?= esc($user['email']) ?></p>
            
            <ul class="profile-info-list">
                <li>
                    <span class="label">Phone</span>
                    <span class="value"><?= esc(($user['phone_code'] ?? '') . ' ' . ($user['phone'] ?? 'Not provided')) ?></span>
                </li>
                <li>
                    <span class="label">WhatsApp</span>
                    <span class="value"><?= esc($user['whatsapp'] ?? 'Not provided') ?></span>
                </li>
                <li>
                    <span class="label">Account Type</span>
                    <span class="value text-capitalize"><?= esc($user['user_type']) ?></span>
                </li>
                <li>
                    <span class="label">Status</span>
                    <span class="value">
                        <?php if ($user['status'] === 'approved'): ?>
                            <span class="text-success">Approved</span>
                        <?php else: ?>
                            <span class="text-danger">Rejected</span>
                        <?php endif; ?>
                    </span>
                </li>
                <li>
                    <span class="label">Member Since</span>
                    <span class="value"><?= date('F d, Y', strtotime($user['created_at'])) ?></span>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header">
                <h5>My Inquiries</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-3">
                    <div class="stat-value mb-2"><?= $inquiryCount ?? 0 ?></div>
                    <p class="text-muted mb-3">Total Inquiries Posted</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="<?= base_url('dashboard/buyer/inquiries') ?>" class="btn btn-outline-success px-4">Manage Inquiries</a>
                        <a href="<?= base_url('dashboard/buyer/inquiries/add') ?>" class="btn btn-success px-4">Post New Inquiry</a>
                    </div>
                </div>

                <?php if (!empty($user['buying_products'])): ?>
                    <hr>
                    <h6>Products You're Looking For</h6>
                    <p class="mb-0"><?= esc($user['buying_products']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($user['requirement'])): ?>
                    <hr>
                    <h6>Additional Requirements</h6>
                    <p class="mb-0"><?= esc($user['requirement']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>
