<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<?php
$statusInfo = [
    'popup_form_filled'  => ['Popup Form Filled', '#6c757d'],
    'email_verified'     => ['Email Verified', '#0d6efd'],
    'account_registered' => ['Account Registered', '#198754'],
];
// Same stage labels/colors as dashboard/admin/leads.php's $stages, so a
// stage reads identically whether it's on a CRM lead or a popup lead.
$stageInfo = [
    'new'                => ['New', '#0d6efd'],
    'trying_to_connect'  => ['Trying to Connect', '#6610f2'],
    'connected_talking'  => ['Connected & Talking', '#6f42c1'],
    'services_pitched'   => ['Services Pitched', '#d63384'],
    'interested_premium' => ['Interested in Premium', '#fd7e14'],
    'contract_sent'      => ['Contract Sent', '#20c997'],
    'not_interested'     => ['Not Interested', '#dc3545'],
    'lead_lost'          => ['Lead Lost', '#6c757d'],
];
$agentLookup = [];
foreach (($agents ?? []) as $a) {
    $agentLookup[$a['id']] = $a['name'];
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
/* Custom themed tooltip for the disabled Edit button -- deliberately not
   Bootstrap's tooltip (which needs a JS init call per element); this is a
   plain CSS hover bubble styled with the same admin theme variables
   (--primary-dark/--primary-gradient) used throughout this layout. */
.locked-edit-wrap { position: relative; display: inline-block; }
.locked-edit-btn {
    font-size: 11px;
    padding: 3px 10px;
    opacity: 0.55;
    cursor: not-allowed;
    background: #e9ecef;
    color: #6c757d;
    border: 1px solid #ced4da;
}
.locked-edit-tooltip {
    position: absolute;
    bottom: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%) translateY(4px);
    background: var(--primary-dark);
    background-image: var(--primary-gradient);
    color: #fff;
    font-size: 11px;
    line-height: 1.4;
    padding: 8px 12px;
    border-radius: 6px;
    white-space: normal;
    width: 200px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.18);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.15s ease, transform 0.15s ease;
    z-index: 20;
}
.locked-edit-tooltip::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 6px solid transparent;
    border-top-color: #5FC86B;
}
.locked-edit-wrap:hover .locked-edit-tooltip {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
}
</style>
<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title"><?= esc($title) ?></h1>
    <span class="badge bg-dark" style="font-size: 14px; padding: 8px 16px;">Total: <?= $pagination['total'] ?? 0 ?> Leads</span>
</div>

<p class="text-muted" style="margin-top:-8px; margin-bottom:16px; font-size:13px;">
    Prospects captured by the site's popup CTA — separate from the leads above, which
    are existing accounts (<code>users</code>) being tracked through the sales pipeline.
    A popup lead only becomes an account once it reaches "Account Registered".
</p>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card card-custom mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Filters & Search</h5>
        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel">Toggle Filters</button>
    </div>
    <div class="collapse show" id="filterPanel">
        <div class="card-body">
            <form method="get" action="<?= current_url() ?>">
                <div class="row g-3">
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Type</label>
                        <select name="user_type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            <option value="buyer" <?= ($filters['user_type'] ?? '') === 'buyer' ? 'selected' : '' ?>>Buyer</option>
                            <option value="supplier" <?= ($filters['user_type'] ?? '') === 'supplier' ? 'selected' : '' ?>>Supplier</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <?php foreach ($statusInfo as $key => $info): ?>
                            <option value="<?= $key ?>" <?= ($filters['status'] ?? '') === $key ? 'selected' : '' ?>><?= $info[0] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Name</label>
                        <input type="text" name="name" class="form-control form-control-sm" value="<?= esc($filters['name'] ?? '') ?>" placeholder="Search name...">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Email</label>
                        <input type="text" name="email" class="form-control form-control-sm" value="<?= esc($filters['email'] ?? '') ?>" placeholder="Search email...">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Phone</label>
                        <input type="text" name="phone" class="form-control form-control-sm" value="<?= esc($filters['phone'] ?? '') ?>" placeholder="Search phone...">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">WhatsApp</label>
                        <select name="whatsapp" class="form-select form-select-sm">
                            <option value="">Any</option>
                            <option value="1" <?= ($filters['whatsapp'] ?? '') === '1' ? 'selected' : '' ?>>Yes</option>
                            <option value="0" <?= ($filters['whatsapp'] ?? '') === '0' ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="<?= esc($filters['date_from'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="<?= esc($filters['date_to'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 col-lg-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm" style="background: var(--primary-gradient); color: #fff; padding: 6px 20px;">Search</button>
                        <a href="<?= current_url() ?>" class="btn btn-sm btn-outline-secondary" style="padding: 6px 20px;">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card card-custom dashb">
    <div class="card-body p-0">
        <?php if (!empty($leads)): ?>
        <div class="table-responsive">
            <table class="table table-custom table-hover table-bordered mb-0">
                <thead>
                    <tr>
                        <?php
                        $currentSort = $filters['sort'] ?? 'created_at';
                        $currentDir  = $filters['sort_dir'] ?? 'DESC';
                        $sortableColumns = [
                            'name'  => 'Name',
                            'email' => 'Email',
                            'phone' => 'Phone',
                        ];
                        foreach ($sortableColumns as $col => $label):
                            $newDir  = ($currentSort === $col && $currentDir === 'ASC') ? 'DESC' : 'ASC';
                            $sortUrl = current_url() . '?' . http_build_query(array_merge($filters, ['sort' => $col, 'sort_dir' => $newDir, 'page' => $pagination['currentPage']]));
                        ?>
                        <th><a href="<?= $sortUrl ?>" class="text-decoration-none" style="color: var(--primary-dark);"><?= $label ?> <?php if ($currentSort === $col): ?><?= $currentDir === 'ASC' ? '&#9650;' : '&#9660;' ?><?php endif; ?></a></th>
                        <?php endforeach; ?>
                        <th>Type</th>
                        <th>Status</th>
                        <?php
                        $dateCol = 'created_at';
                        $dateDir = ($currentSort === $dateCol && $currentDir === 'ASC') ? 'DESC' : 'ASC';
                        $dateSortUrl = current_url() . '?' . http_build_query(array_merge($filters, ['sort' => $dateCol, 'sort_dir' => $dateDir, 'page' => $pagination['currentPage']]));
                        ?>
                        <th><a href="<?= $dateSortUrl ?>" class="text-decoration-none" style="color: var(--primary-dark);">Submitted <?php if ($currentSort === $dateCol): ?><?= $currentDir === 'ASC' ? '&#9650;' : '&#9660;' ?><?php endif; ?></a></th>
                        <th>Email Verified</th>
                        <th>Agent</th>
                        <th>Stage</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td style="font-size:12px;"><?= esc($lead['name']) ?></td>
                        <td style="font-size:12px;"><?= esc($lead['email']) ?></td>
                        <td style="font-size:12px; white-space:nowrap;">
                            <?= esc(trim(($lead['phone_code'] ?? '') . ' ' . ($lead['phone'] ?? '')) ?: '-') ?>
                            <?php if (!empty($lead['whatsapp'])): ?>
                                <i class="fab fa-whatsapp" style="color:#25D366; margin-left:4px; font-size:14px;" title="WhatsApp Available"></i>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;"><?= esc(ucfirst($lead['user_type'])) ?></td>
                        <td>
                            <?php $info = $statusInfo[$lead['status']] ?? [ucfirst(str_replace('_', ' ', $lead['status'])), '#6c757d']; ?>
                            <span class="badge" style="background:<?= $info[1] ?>20; color:<?= $info[1] ?>; border:1px solid <?= $info[1] ?>; font-size:10px; padding:4px 8px;"><?= $info[0] ?></span>
                        </td>
                        <td style="font-size:11px; white-space:nowrap;">
                            <?= date('M d, Y', strtotime($lead['created_at'])) ?><br>
                            <small style="color:#999;"><?= date('h:i A', strtotime($lead['created_at'])) ?></small>
                        </td>
                        <td style="font-size:11px; white-space:nowrap;">
                            <?php if (!empty($lead['verified_at'])): ?>
                                <?= date('M d, Y', strtotime($lead['verified_at'])) ?>
                            <?php else: ?>
                                <span style="color:#ccc;">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:11px; white-space:nowrap;">
                            <?php
                                $assignedAgentId = $lead['assigned_agent_id'] ?? null;
                                $agentName = $assignedAgentId ? ($agentLookup[$assignedAgentId] ?? null) : null;
                            ?>
                            <?php if ($agentName): ?>
                                <span style="color:var(--primary-teal); font-weight:500;"><?= esc($agentName) ?></span>
                            <?php else: ?>
                                <span style="color:#ccc;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $stage = $stageInfo[$lead['lead_stage'] ?? 'new'] ?? ['New', '#0d6efd']; ?>
                            <span class="badge" style="background:<?= $stage[1] ?>20; color:<?= $stage[1] ?>; border:1px solid <?= $stage[1] ?>; font-size:10px; padding:4px 8px;"><?= $stage[0] ?></span>
                        </td>
                        <td style="min-width:160px;">
                            <?php $lastNote = ($latest_notes ?? [])[$lead['id']] ?? ''; ?>
                            <?php if ($lastNote): ?>
                                <div class="last-note-text" data-lead-id="<?= $lead['id'] ?>" style="font-size:10px; color:#6c757d; margin-bottom:4px; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= esc($lastNote) ?>"><?= esc($lastNote) ?></div>
                            <?php else: ?>
                                <div class="last-note-text" data-lead-id="<?= $lead['id'] ?>" style="font-size:10px; color:#6c757d; margin-bottom:4px; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></div>
                            <?php endif; ?>
                            <div class="d-flex flex-column gap-1">
                                <input type="text" class="form-control form-control-sm popup-note-input" data-lead-id="<?= $lead['id'] ?>" placeholder="Add note..." style="font-size:11px;">
                                <button class="btn btn-sm btn-outline-primary popup-save-note-btn" data-lead-id="<?= $lead['id'] ?>" style="font-size:10px; padding:4px 8px;">Save</button>
                            </div>
                        </td>
                        <td style="white-space:nowrap;">
                            <?php if ($lead['status'] === 'account_registered'): ?>
                                <span class="locked-edit-wrap">
                                    <button type="button" class="btn btn-sm locked-edit-btn" disabled>Edit</button>
                                    <span class="locked-edit-tooltip">Registered leads can't be edited here — this lead already has an account in Manage Users.</span>
                                </span>
                            <?php else: ?>
                                <a href="<?= base_url('leads/popup/edit/' . $lead['id']) ?>" class="btn btn-sm btn-outline-primary" style="font-size:11px; padding:3px 10px;">Edit</a>
                            <?php endif; ?>
                            <form method="post" action="<?= base_url('leads/popup/delete/' . $lead['id']) ?>" class="d-inline" onsubmit="return confirm('Delete this popup lead? This cannot be undone.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size:11px; padding:3px 10px;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pagination['totalPages'] > 1): ?>
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
            <div style="font-size: 13px; color: #6c757d;">
                Showing <?= (($pagination['currentPage'] - 1) * $pagination['perPage']) + 1 ?> to <?= min($pagination['currentPage'] * $pagination['perPage'], $pagination['total']) ?> of <?= $pagination['total'] ?> leads
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($pagination['currentPage'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= current_url() . '?' . http_build_query(array_merge($filters, ['page' => $pagination['currentPage'] - 1])) ?>">Prev</a>
                    </li>
                    <?php endif; ?>

                    <?php
                    $startPage = max(1, $pagination['currentPage'] - 2);
                    $endPage   = min($pagination['totalPages'], $pagination['currentPage'] + 2);
                    for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?= $i === $pagination['currentPage'] ? 'active' : '' ?>">
                        <a class="page-link" href="<?= current_url() . '?' . http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($pagination['currentPage'] < $pagination['totalPages']): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= current_url() . '?' . http_build_query(array_merge($filters, ['page' => $pagination['currentPage'] + 1])) ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="text-center p-5">
            <p class="text-muted mb-0">No popup leads found matching your criteria.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.querySelectorAll('.popup-save-note-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const leadId = this.dataset.leadId;
        const saveBtn = this;
        const input = document.querySelector('.popup-note-input[data-lead-id="' + leadId + '"]');
        const note = input.value.trim();
        if (!note) return;
        saveBtn.disabled = true;
        saveBtn.textContent = '...';
        fetch('<?= base_url("leads/popup/add-note") ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: 'lead_id=' + leadId + '&note=' + encodeURIComponent(note)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                var noteDisplay = document.querySelector('.last-note-text[data-lead-id="' + leadId + '"]');
                if (noteDisplay) {
                    noteDisplay.textContent = note;
                    noteDisplay.title = note;
                }
                input.value = '';
                saveBtn.textContent = 'Saved';
                saveBtn.classList.remove('btn-outline-primary');
                saveBtn.classList.add('btn-success');
                setTimeout(function() {
                    saveBtn.textContent = 'Save';
                    saveBtn.classList.remove('btn-success');
                    saveBtn.classList.add('btn-outline-primary');
                    saveBtn.disabled = false;
                }, 2000);
            } else {
                alert(data.message || 'Failed to save note');
                saveBtn.textContent = 'Save';
                saveBtn.disabled = false;
            }
        })
        .catch(function() {
            alert('Network error');
            saveBtn.textContent = 'Save';
            saveBtn.disabled = false;
        });
    });
});
</script>
<?= $this->endSection() ?>
