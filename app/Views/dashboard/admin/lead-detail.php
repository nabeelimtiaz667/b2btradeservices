<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$stages = [
    'new' => ['New', '#0d6efd'],
    'trying_to_connect' => ['Trying to Connect', '#6610f2'],
    'connected_talking' => ['Connected & Talking', '#6f42c1'],
    'services_pitched' => ['Services Pitched', '#d63384'],
    'interested_premium' => ['Interested in Premium', '#fd7e14'],
    'contract_sent' => ['Contract Sent', '#20c997'],
    'not_interested' => ['Not Interested', '#dc3545'],
    'lead_lost' => ['Lead Lost', '#6c757d'],
];
$membershipColors = [
    'free' => '#6c757d',
    'silver' => '#adb5bd',
    'gold' => '#ffc107',
    'platinum' => '#6610f2',
    'vip' => '#dc3545',
];
?>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?= base_url('leads/all') ?>" class="text-decoration-none" style="color: var(--primary-teal); font-size: 14px;">&larr; Back to Leads</a>
        <h1 class="page-title mt-1">Lead: <?= esc($lead['uid']) ?> - <?= esc($lead['name']) ?></h1>
    </div>
    <div>
        <span class="badge" style="background: <?= $lead['user_type'] === 'buyer' ? '#0d6efd' : '#198754' ?>; font-size: 14px; padding: 8px 16px;"><?= ucfirst($lead['user_type']) ?></span>
        <?php $stageInfo = $stages[$lead['lead_stage'] ?? 'new'] ?? ['New', '#0d6efd']; ?>
        <span class="badge" style="background: <?= $stageInfo[1] ?>; font-size: 14px; padding: 8px 16px;"><?= $stageInfo[0] ?></span>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-custom mb-4">
            <div class="card-header"><h5>Lead Information</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="profile-info-list">
                            <li><span class="label">UID</span><span class="value"><?= esc($lead['uid']) ?></span></li>
                            <li><span class="label">Full Name</span><span class="value"><?= esc($lead['name']) ?></span></li>
                            <li><span class="label">Email</span><span class="value"><?= esc($lead['email']) ?></span></li>
                            <li><span class="label">Phone</span><span class="value"><?= esc($lead['phone'] ?? 'N/A') ?> <?= !empty($lead['whatsapp']) ? '<span style="color:#25D366">(WhatsApp)</span>' : '' ?></span></li>
                            <li><span class="label">Company</span><span class="value"><?= esc($lead['company_name'] ?? 'N/A') ?></span></li>
                            <li><span class="label">Website</span><span class="value"><?php if (!empty($lead['website'])): ?><a href="<?= esc($lead['website']) ?>" target="_blank"><?= esc($lead['website']) ?></a><?php else: ?>N/A<?php endif; ?></span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="profile-info-list">
                            <li><span class="label">City</span><span class="value"><?= esc($lead['city'] ?? 'N/A') ?></span></li>
                            <li><span class="label">Country</span><span class="value"><?= esc($country['name'] ?? 'N/A') ?></span></li>
                            <li><span class="label">Lead Type</span><span class="value"><?= ucfirst($lead['user_type']) ?></span></li>
                            <li><span class="label">Lead Source</span><span class="value"><?= esc($lead['lead_source'] ?? 'N/A') ?></span></li>
                            <li><span class="label">Registration Date</span><span class="value"><?= date('M d, Y H:i', strtotime($lead['created_at'])) ?></span></li>
                            <li><span class="label">Landing Page</span><span class="value" style="font-size: 12px; word-break: break-all;"><?= esc($lead['landing_page_url'] ?? 'N/A') ?></span></li>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6 style="color: var(--primary-dark); font-weight: 600;">Products</h6>
                        <?php if ($lead['user_type'] === 'supplier' && !empty($lead['selling_products'])): ?>
                            <p><strong>Selling:</strong> <?= esc($lead['selling_products']) ?></p>
                        <?php endif; ?>
                        <?php if ($lead['user_type'] === 'buyer' && !empty($lead['buying_products'])): ?>
                            <p><strong>Buying:</strong> <?= esc($lead['buying_products']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($lead['requirement'])): ?>
                            <p><strong>Requirements:</strong> <?= esc($lead['requirement']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h6 style="color: var(--primary-dark); font-weight: 600;">Membership</h6>
                        <span class="badge" style="background: <?= $membershipColors[$lead['membership_level'] ?? 'free'] ?>; font-size: 14px; padding: 6px 14px; color: <?= ($lead['membership_level'] ?? 'free') === 'gold' ? '#000' : '#fff' ?>;"><?= ucfirst($lead['membership_level'] ?? 'free') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom mb-4" id="notes">
            <div class="card-header"><h5>Agent Notes</h5></div>
            <div class="card-body">
                <form method="post" action="<?= base_url('leads/add-note') ?>" class="mb-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                    <div class="mb-3">
                        <textarea name="note" class="form-control" rows="3" placeholder="Add a note about this lead..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-sm" style="background: var(--primary-gradient); color: #fff;">Add Note</button>
                </form>

                <?php if (!empty($notes)): ?>
                <div class="notes-timeline">
                    <?php foreach ($notes as $note): ?>
                    <div class="p-3 mb-3 rounded" style="background: #f8f9fa; border-left: 3px solid var(--primary-teal);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <strong style="color: var(--primary-dark);"><?= esc($note['agent_name'] ?? 'Unknown Agent') ?></strong>
                            <small class="text-muted"><?= date('M d, Y H:i', strtotime($note['created_at'])) ?></small>
                        </div>
                        <p class="mb-0" style="white-space: pre-wrap;"><?= esc($note['note']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted text-center">No notes yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card card-custom mb-4">
            <div class="card-header"><h5>Activity Timeline</h5></div>
            <div class="card-body">
                <?php if (!empty($activities)): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Activity</th>
                                <th>Description</th>
                                <th>Device</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activities as $activity): ?>
                            <tr>
                                <td style="font-size: 13px; white-space: nowrap;"><?= !empty($activity['created_at']) ? date('M d, Y H:i:s', strtotime($activity['created_at'])) : '-' ?></td>
                                <td>
                                    <?php
                                    $typeColors = [
                                        'registration' => '#198754',
                                        'login' => '#0d6efd',
                                        'logout' => '#6c757d',
                                        'stage_change' => '#6610f2',
                                        'agent_assigned' => '#fd7e14',
                                        'membership_change' => '#ffc107',
                                        'page_visit' => '#20c997',
                                    ];
                                    $color = $typeColors[$activity['activity_type']] ?? '#6c757d';
                                    ?>
                                    <span class="badge" style="background: <?= $color ?>;"><?= ucwords(str_replace('_', ' ', $activity['activity_type'])) ?></span>
                                </td>
                                <td style="font-size: 13px;"><?= esc($activity['description'] ?? '') ?></td>
                                <td style="font-size: 13px;"><?= esc($activity['device_type'] ?? '-') ?></td>
                                <td style="font-size: 13px;"><?= esc($activity['ip_address'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center">No activity recorded yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-custom mb-4">
            <div class="card-header"><h5>Update Lead Stage</h5></div>
            <div class="card-body">
                <form method="post" action="<?= base_url('leads/update-stage') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                    <div class="mb-3">
                        <select name="lead_stage" class="form-select">
                            <?php foreach ($lead_stages as $key => $label): ?>
                            <option value="<?= $key ?>" <?= ($lead['lead_stage'] ?? 'new') === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm w-100" style="background: var(--primary-gradient); color: #fff;">Update Stage</button>
                </form>
            </div>
        </div>

        <?php if (($user['user_type'] ?? '') === 'admin'): ?>
        <div class="card card-custom mb-4">
            <div class="card-header"><h5>Assign Agent</h5></div>
            <div class="card-body">
                <form method="post" action="<?= base_url('leads/assign-agent') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                    <div class="mb-3">
                        <select name="agent_id" class="form-select">
                            <option value="">Unassigned</option>
                            <?php foreach ($agents as $agent): ?>
                            <option value="<?= $agent['id'] ?>" <?= ($lead['assigned_agent_id'] ?? '') == $agent['id'] ? 'selected' : '' ?>><?= esc($agent['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm w-100" style="background: var(--primary-gradient); color: #fff;">Assign Agent</button>
                </form>
                <?php if ($assigned_agent): ?>
                <div class="mt-3 p-2 rounded" style="background: #f8f9fa;">
                    <small class="text-muted">Currently assigned to:</small><br>
                    <strong><?= esc($assigned_agent['name']) ?></strong>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card card-custom mb-4">
            <div class="card-header"><h5>Update Membership</h5></div>
            <div class="card-body">
                <form method="post" action="<?= base_url('leads/update-membership') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                    <div class="mb-3">
                        <select name="membership_level" class="form-select">
                            <?php foreach ($membership_levels as $key => $label): ?>
                            <option value="<?= $key ?>" <?= ($lead['membership_level'] ?? 'free') === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm w-100" style="background: var(--primary-gradient); color: #fff;">Update Membership</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="card card-custom mb-4">
            <div class="card-header"><h5>Session Info</h5></div>
            <div class="card-body">
                <ul class="profile-info-list">
                    <li><span class="label">Last Login</span><span class="value"><?= !empty($lead['last_login_at']) ? date('M d, Y H:i', strtotime($lead['last_login_at'])) : 'Never' ?></span></li>
                    <li><span class="label">Last IP</span><span class="value"><?= esc($lead['last_login_ip'] ?? 'N/A') ?></span></li>
                    <li><span class="label">Device</span><span class="value"><?= esc($lead['last_device_type'] ?? 'N/A') ?></span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
