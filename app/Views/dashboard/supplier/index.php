<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Welcome, <?= esc($user['name']) ?>!</h1>
        <p class="text-muted mb-0">Supplier Dashboard</p>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>My Products</h5>
                <a href="<?= base_url('dashboard/supplier/products') ?>" class="btn btn-sm btn-outline-success">Manage Products</a>
            </div>
            <div class="card-body text-center">
                <div class="stat-icon mx-auto mb-3" style="width:60px;height:60px;background:linear-gradient(45deg,#15A2A0,#5FC86B);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#fff" viewBox="0 0 16 16">
                        <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                    </svg>
                </div>
                <div class="stat-value" style="font-size:32px;font-weight:700;color:#0A504F;"><?= $productCount ?? 0 ?></div>
                <div class="stat-label text-muted">Total Products</div>
                <a href="<?= base_url('dashboard/supplier/products/add') ?>" class="btn btn-success mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    Add New Product
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>
