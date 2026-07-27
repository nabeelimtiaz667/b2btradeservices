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
$countryLookup = [];
foreach ($countries as $c) {
    $countryLookup[$c['id']] = $c;
}
$agentLookup = [];
foreach (($agents ?? []) as $a) {
    $agentLookup[$a['id']] = $a['name'];
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title"><?= esc($title) ?></h1>
    <span class="badge bg-dark" style="font-size: 14px; padding: 8px 16px;">Total: <?= $pagination['total'] ?? 0 ?> Leads</span>
</div>

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
                    <?php if ($page_type === 'all'): ?>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Lead Type</label>
                        <select name="lead_type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            <option value="buyer" <?= ($filters['lead_type'] ?? '') === 'buyer' ? 'selected' : '' ?>>Buyer</option>
                            <option value="supplier" <?= ($filters['lead_type'] ?? '') === 'supplier' ? 'selected' : '' ?>>Supplier</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">UID</label>
                        <input type="text" name="uid" class="form-control form-control-sm" value="<?= esc($filters['uid'] ?? '') ?>" placeholder="Search UID...">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Name</label>
                        <input type="text" name="name" class="form-control form-control-sm" value="<?= esc($filters['name'] ?? '') ?>" placeholder="Search name...">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm" value="<?= esc($filters['email'] ?? '') ?>" placeholder="Full email address..." pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
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
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Country</label>
                        <select name="country_id" class="form-select form-select-sm">
                            <option value="">All Countries</option>
                            <?php foreach ($countries as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($filters['country_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Membership</label>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start" type="button" data-bs-toggle="dropdown" style="font-size:12px;">
                                <?php 
                                $selectedLevels = array_filter(explode(',', $filters['membership_level'] ?? ''));
                                echo !empty($selectedLevels) ? count($selectedLevels) . ' selected' : 'All Levels';
                                ?>
                            </button>
                            <div class="dropdown-menu p-2" style="min-width:160px;">
                                <?php foreach ($membership_levels as $key => $label): ?>
                                <div class="form-check">
                                    <input class="form-check-input membership-check" type="checkbox" value="<?= $key ?>" id="mem_<?= $key ?>" <?= in_array($key, $selectedLevels) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="mem_<?= $key ?>" style="font-size:12px;"><?= $label ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <input type="hidden" name="membership_level" id="membership_level_hidden" value="<?= esc($filters['membership_level'] ?? '') ?>">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Lead Stage</label>
                        <select name="lead_stage" class="form-select form-select-sm">
                            <option value="">All Stages</option>
                            <?php foreach ($lead_stages as $key => $label): ?>
                            <option value="<?= $key ?>" <?= ($filters['lead_stage'] ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (($user['user_type'] ?? '') === 'admin'): ?>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Assigned Agent</label>
                        <select name="assigned_agent_id" class="form-select form-select-sm">
                            <option value="">All Agents</option>
                            <?php foreach ($agents as $agent): ?>
                            <option value="<?= $agent['id'] ?>" <?= ($filters['assigned_agent_id'] ?? '') == $agent['id'] ? 'selected' : '' ?>><?= esc($agent['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Lead Source</label>
                        <input type="text" name="lead_source" class="form-control form-control-sm" value="<?= esc($filters['lead_source'] ?? '') ?>" placeholder="Source...">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" style="font-size: 13px; font-weight: 600;">Products</label>
                        <input type="text" name="products" class="form-control form-control-sm" value="<?= esc($filters['products'] ?? '') ?>" placeholder="Search products...">
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
                        $currentDir = $filters['sort_dir'] ?? 'DESC';
                        $sortableColumns = [
                            'uid' => 'UID',
                            'name' => 'Name',
                            'country_id' => '',
                            'company_name' => 'Company',
                            'phone' => 'Phone',
                            'email' => 'Email',
                            'lead_source' => 'Source',
                        ];
                        foreach ($sortableColumns as $col => $label):
                            $newDir = ($currentSort === $col && $currentDir === 'ASC') ? 'DESC' : 'ASC';
                            $sortUrl = current_url() . '?' . http_build_query(array_merge($filters, ['sort' => $col, 'sort_dir' => $newDir, 'page' => $pagination['currentPage']]));
                        ?>
                        <th><a href="<?= $sortUrl ?>" class="text-decoration-none" style="color: var(--primary-dark);"><?= $label ?><?php if ($col === 'phone'): ?> <i class="fab fa-whatsapp" style="color:#25D366;"></i><?php endif; ?> <?php if ($currentSort === $col): ?><?= $currentDir === 'ASC' ? '&#9650;' : '&#9660;' ?><?php endif; ?></a></th>
                        <?php endforeach; ?>
                        <th>Products</th>
                        <th>Date</th>
                        <th>Stage</th>
                        <th>Agent</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td><a href="<?= base_url('leads/detail/' . $lead['uid']) ?>" style="color: var(--primary-teal); font-weight: 600; font-size:12px;"><?= esc(str_replace(['SUP-', 'BUY-', 'AGT-'], ['S-', 'B-', 'A-'], $lead['uid'])) ?></a></td>
                        <td style="font-size:12px;"><?= esc($lead['name']) ?></td>
                        <?php $country = $countryLookup[$lead['country_id']] ?? null; ?>
                        <td style="text-align:center;">
                            <?php if ($country && !empty($country['flag'])): ?>
                                <img src="<?= base_url('assets/images/flags/' . $country['flag']) ?>" alt="<?= esc($country['name']) ?>" title="<?= esc($country['name']) ?>" style="width:24px; height:24px; border-radius:50%; object-fit:cover;">
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td style="font-size:12px;">
                            <?php if (!empty($lead['company_name'])): ?>
                                <?php
                                    $profileUrl = '';
                                    if ($lead['user_type'] === 'supplier') {
                                        $profileUrl = base_url('supplier/profile/' . ($lead['slug'] ?? $lead['id']));
                                    } elseif ($lead['user_type'] === 'buyer') {
                                        $profileUrl = base_url('buyer/detail/' . $lead['id']);
                                    }
                                ?>
                                <?php if ($profileUrl): ?>
                                    <a href="<?= $profileUrl ?>" target="_blank" style="color:var(--primary-teal);"><?= esc($lead['company_name']) ?></a>
                                <?php else: ?>
                                    <?= esc($lead['company_name']) ?>
                                <?php endif; ?>
                            <?php else: ?>-<?php endif; ?>
                            <?php
                                $ml = $lead['membership_level'] ?? 'free';
                                $mlColors = ['free' => '#6c757d', 'silver' => '#adb5bd', 'gold' => '#ffc107', 'platinum' => '#6610f2', 'vip' => '#dc3545'];
                                $mlColor = $mlColors[$ml] ?? '#6c757d';
                                $mlTextColor = $ml === 'gold' ? '#000' : '#fff';
                            ?>
                            <br><span class="badge" style="background:<?= $mlColor ?>; color:<?= $mlTextColor ?>; font-size:9px; padding:2px 6px;"><?= ucfirst($ml) ?></span>
                        </td>
                        <td style="font-size:12px; white-space:nowrap;">
                            <?= esc($lead['phone'] ?? '-') ?>
                            <?php if (!empty($lead['whatsapp'])): ?>
                                <i class="fab fa-whatsapp" style="color:#25D366; margin-left:4px; font-size:14px;" title="WhatsApp Available"></i>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;"><?= esc($lead['email']) ?></td>
                        <td style="font-size:12px;"><?= esc($lead['lead_source'] ?? '-') ?></td>
                        <td style="font-size:12px; max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            <?php
                            $products = $lead['user_type'] === 'supplier' ? ($lead['selling_products'] ?? '') : ($lead['buying_products'] ?? '');
                            echo esc($products ?: '-');
                            ?>
                        </td>
                        <td style="font-size:11px; white-space:nowrap;">
                            <?= date('M d, Y', strtotime($lead['created_at'])) ?><br>
                            <small style="color:#999;"><?= date('h:i A', strtotime($lead['created_at'])) ?></small>
                        </td>
                        <td>
                            <?php $stageInfo = $stages[$lead['lead_stage'] ?? 'new'] ?? ['New', '#0d6efd']; ?>
                            <select class="form-select form-select-sm stage-select" data-lead-id="<?= $lead['id'] ?>" style="font-size:11px; padding:2px 24px 2px 6px; min-width:130px; background-color:<?= $stageInfo[1] ?>20; border-color:<?= $stageInfo[1] ?>;">
                                <?php foreach ($stages as $key => $info): ?>
                                    <option value="<?= $key ?>" <?= ($lead['lead_stage'] ?? 'new') === $key ? 'selected' : '' ?>><?= $info[0] ?></option>
                                <?php endforeach; ?>
                            </select>
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
                        <td style="min-width:160px;">
                            <?php $lastNote = ($latest_notes ?? [])[$lead['id']] ?? ''; ?>
                            <?php if ($lastNote): ?>
                                <div class="last-note-text" data-lead-id="<?= $lead['id'] ?>" style="font-size:10px; color:#6c757d; margin-bottom:4px; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= esc($lastNote) ?>"><?= esc($lastNote) ?></div>
                            <?php else: ?>
                                <div class="last-note-text" data-lead-id="<?= $lead['id'] ?>" style="font-size:10px; color:#6c757d; margin-bottom:4px; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></div>
                            <?php endif; ?>
                            <div class="d-flex flex-column gap-1">
                                <input type="text" class="form-control form-control-sm note-input" data-lead-id="<?= $lead['id'] ?>" placeholder="Add note..." style="font-size:11px;">
                                <button class="btn btn-sm btn-outline-primary save-note-btn" data-lead-id="<?= $lead['id'] ?>" style="font-size:10px; padding:4px 8px;">Save</button>
                            </div>
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
                    $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);
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
            <p class="text-muted mb-0">No leads found matching your criteria.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.querySelectorAll('.stage-select').forEach(function(select) {
    select.addEventListener('change', function() {
        const leadId = this.dataset.leadId;
        const stage = this.value;
        fetch('<?= base_url("leads/ajax-update-stage") ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: 'lead_id=' + leadId + '&lead_stage=' + stage
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                this.style.backgroundColor = '';
                this.classList.add('border-success');
                setTimeout(() => this.classList.remove('border-success'), 2000);
            } else {
                alert(data.message || 'Failed to update stage');
            }
        })
        .catch(() => alert('Network error'));
    });
});

document.querySelectorAll('.save-note-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const leadId = this.dataset.leadId;
        const saveBtn = this;
        const input = document.querySelector('.note-input[data-lead-id="' + leadId + '"]');
        const note = input.value.trim();
        if (!note) return;
        saveBtn.disabled = true;
        saveBtn.textContent = '...';
        fetch('<?= base_url("leads/ajax-add-note") ?>', {
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

document.querySelectorAll('.membership-check').forEach(function(cb) {
    cb.addEventListener('change', function() {
        const checked = document.querySelectorAll('.membership-check:checked');
        const values = Array.from(checked).map(c => c.value).join(',');
        document.getElementById('membership_level_hidden').value = values;
    });
});
</script>
<?= $this->endSection() ?>
