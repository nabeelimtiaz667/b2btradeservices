<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">Manage Buyer Inquiries</h1>
    <a href="<?= base_url('dashboard/inquiries/add') ?>" class="btn" style="background: var(--primary-gradient); color: #fff;">Add Inquiry</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card card-custom mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Search</label>
                <input type="text" id="filterSearch" class="form-control" placeholder="Search title, buyer, product...">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select id="filterStatus" class="form-select">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="inactive">Inactive</option>
                    <option value="closed">Closed</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Country</label>
                <select id="filterCountry" class="form-select">
                    <option value="">All Countries</option>
                    <?php foreach ($countries as $c): ?>
                        <option value="<?= esc($c['name']) ?>"><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Date From</label>
                <input type="date" id="filterDateFrom" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Date To</label>
                <input type="date" id="filterDateTo" class="form-control">
            </div>
            <div class="col-md-1">
                <button type="button" id="filterReset" class="btn btn-outline-secondary w-100">Reset</button>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body">
        <?php if (!empty($inquiries)): ?>
        <div class="table-responsive">
            <table class="table table-custom table-bordered" id="inquiriesTable">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Product</th>
                        <th>Buyer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th>Time of Registration</th>
                        <th>Agent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $i):
                        $countryName = $i['country']['name'] ?? '';
                        $countryFlag = '';
                        if (!empty($i['country']['name'] ?? '')) {
                            $flagName = strtolower($i['country']['name']);
                            $flagName = str_replace(' ', '-', $flagName);
                            $countryFlag = $flagName;
                        }
                        $createdDate = !empty($i['created_at']) ? date('d M Y H:i', strtotime($i['created_at'])) : '-';
                    ?>
                    <tr data-title="<?= esc(strtolower($i['title'])) ?>"
                        data-product="<?= esc(strtolower($i['product_name'] ?? '')) ?>"
                        data-buyer="<?= esc(strtolower($i['buyer_name'] ?? '')) ?>"
                        data-status="<?= esc($i['status']) ?>"
                        data-country="<?= esc($countryName) ?>"
                        data-date="<?= !empty($i['created_at']) ? date('Y-m-d', strtotime($i['created_at'])) : '' ?>">
                        <td><a href="<?= inquiry_url($i) ?>" target="_blank" style="color: #0d6efd; text-decoration: underline;"><?= esc(mb_substr($i['title'], 0, 40)) ?><?= mb_strlen($i['title']) > 40 ? '...' : '' ?></a></td>
                        <td><?= esc($i['product_name'] ?? 'N/A') ?></td>
                        <td><?= esc($i['buyer_name']) ?></td>
                        <td><?= esc($i['buyer_email'] ?? '-') ?></td>
                        <td><?= esc($i['buyer_phone'] ?? '-') ?></td>
                        <td>
                            <?php if (!empty($countryFlag)): ?>
                                <img src="<?= base_url('assets/images/flags/' . $countryFlag . '.svg') ?>" alt="<?= esc($countryName) ?>" style="width:24px;height:24px;border-radius:50%;object-fit:cover;margin-right:5px;vertical-align:middle;">
                            <?php endif; ?>
                            <?= esc($countryName ?: 'N/A') ?>
                        </td>
                        <td>
                            <?php if ($i['status'] === 'active'): ?>
                                <span class="badge badge-approved">Active</span>
                            <?php elseif ($i['status'] === 'closed'): ?>
                                <span class="badge badge-pending">Closed</span>
                            <?php else: ?>
                                <span class="badge badge-rejected"><?= ucfirst($i['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= $createdDate ?></td>
                        <td><?= esc($i['agent_name'] ?? '-') ?></td>
                        <td>
                            <a href="<?= base_url('dashboard/inquiries/edit/' . $i['id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="<?= base_url('dashboard/inquiries/delete/' . $i['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this inquiry?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-muted text-center mb-0">No buyer inquiries found.</p>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('filterSearch');
    var statusSelect = document.getElementById('filterStatus');
    var countrySelect = document.getElementById('filterCountry');
    var dateFrom = document.getElementById('filterDateFrom');
    var dateTo = document.getElementById('filterDateTo');
    var resetBtn = document.getElementById('filterReset');
    var rows = document.querySelectorAll('#inquiriesTable tbody tr');

    function applyFilters() {
        var search = searchInput.value.toLowerCase().trim();
        var status = statusSelect.value;
        var country = countrySelect.value;
        var from = dateFrom.value;
        var to = dateTo.value;

        rows.forEach(function(row) {
            var show = true;

            if (search) {
                var title = row.getAttribute('data-title') || '';
                var product = row.getAttribute('data-product') || '';
                var buyer = row.getAttribute('data-buyer') || '';
                if (title.indexOf(search) === -1 && product.indexOf(search) === -1 && buyer.indexOf(search) === -1) {
                    show = false;
                }
            }

            if (show && status) {
                if (row.getAttribute('data-status') !== status) {
                    show = false;
                }
            }

            if (show && country) {
                if (row.getAttribute('data-country') !== country) {
                    show = false;
                }
            }

            if (show && from) {
                var rowDate = row.getAttribute('data-date');
                if (!rowDate || rowDate < from) {
                    show = false;
                }
            }

            if (show && to) {
                var rowDate = row.getAttribute('data-date');
                if (!rowDate || rowDate > to) {
                    show = false;
                }
            }

            row.style.display = show ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', applyFilters);
    statusSelect.addEventListener('change', applyFilters);
    countrySelect.addEventListener('change', applyFilters);
    dateFrom.addEventListener('change', applyFilters);
    dateTo.addEventListener('change', applyFilters);

    resetBtn.addEventListener('click', function() {
        searchInput.value = '';
        statusSelect.value = '';
        countrySelect.value = '';
        dateFrom.value = '';
        dateTo.value = '';
        applyFilters();
    });
});
</script>
<?= $this->endSection() ?>
