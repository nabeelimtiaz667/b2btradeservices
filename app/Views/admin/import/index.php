<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h1 class="page-title">Import Data (CSV)</h1>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card card-custom mb-4">
    <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'suppliers') ? 'active' : '' ?>" href="<?= base_url('admin/import/suppliers') ?>">Suppliers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'products') ? 'active' : '' ?>" href="<?= base_url('admin/import/products') ?>">Products</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'inquiries') ? 'active' : '' ?>" href="<?= base_url('admin/import/inquiries') ?>">Buyer Inquiries</a>
            </li>
        </ul>

        <div class="tab-content pt-4">
            <div class="tab-pane fade show active">
                <div class="row">
                    <div class="col-lg-7">
                        <h5 class="mb-3">
                            Import <?= ucfirst($activeTab) ?>
                        </h5>
                        <p class="text-muted mb-3">
                            Upload a CSV file to bulk import <?= $activeTab ?> into the system.
                            Download the sample template to see the expected format.
                        </p>

                        <a href="<?= base_url('assets/csv/sample_' . $activeTab . '.csv') ?>" class="btn btn-outline-secondary btn-sm mb-3" download>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                            </svg>
                            Download Sample CSV
                        </a>

                        <form method="post" action="<?= base_url('admin/import/' . $activeTab) ?>" enctype="multipart/form-data" id="importForm">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select CSV File</label>
                                <input type="file" class="form-control" name="csv_file" id="csvFileInput" accept=".csv" required>
                            </div>

                            <div id="csvPreview" style="display:none;" class="mb-3">
                                <label class="form-label fw-bold">Preview (first 5 rows)</label>
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm table-bordered" id="previewTable">
                                    </table>
                                </div>
                            </div>

                            <button type="submit" class="btn" style="background: var(--primary-gradient); color: #fff;" id="importBtn" disabled>
                                Import <?= ucfirst($activeTab) ?>
                            </button>
                        </form>
                    </div>

                    <div class="col-lg-5">
                        <div class="p-3 rounded" style="background: #f0f9f4; border: 1px solid #c3e6cb;">
                            <h6 class="mb-2" style="color: var(--primary-dark);">CSV Format Guide</h6>
                            <?php if ($activeTab === 'suppliers'): ?>
                                <p class="small text-muted mb-1"><strong>Required columns:</strong> name, email</p>
                                <p class="small text-muted mb-1"><strong>Optional columns:</strong> phone, whatsapp, country, company_name, company_introduction, website, city, selling_products, membership_level</p>
                                <p class="small text-muted mb-1"><strong>Country:</strong> Must match an existing country name (e.g., "United States", "China")</p>
                                <p class="small text-muted mb-0"><strong>Membership Level:</strong> free, starter, gold, platinum, or vip (defaults to free)</p>
                            <?php elseif ($activeTab === 'products'): ?>
                                <p class="small text-muted mb-1"><strong>Required columns:</strong> name, supplier_email</p>
                                <p class="small text-muted mb-1"><strong>Optional columns:</strong> category, description, specifications, min_order_quantity, min_order_unit, price_range, supply_ability, delivery_time, packaging, port, payment_terms, certifications</p>
                                <p class="small text-muted mb-1"><strong>supplier_email:</strong> Must match an existing supplier's email</p>
                                <p class="small text-muted mb-0"><strong>Category:</strong> Must match an existing category name</p>
                            <?php else: ?>
                                <p class="small text-muted mb-1"><strong>Required columns:</strong> title</p>
                                <p class="small text-muted mb-1"><strong>Optional columns:</strong> buyer_name, buyer_email, buyer_phone, buyer_whatsapp, buyer_company, category, country, product_name, quantity, unit, target_price, description, payment_terms, shipping_terms, destination_port</p>
                                <p class="small text-muted mb-1"><strong>Country &amp; Category:</strong> Must match existing names</p>
                                <p class="small text-muted mb-0"><strong>Duplicates:</strong> Rows with same buyer_email + title are skipped</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($results)): ?>
<div class="card card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Import Results</h5>
        <div>
            <span class="badge bg-success me-2"><?= $results['imported'] ?> Imported</span>
            <span class="badge bg-warning text-dark me-2"><?= $results['skipped'] ?> Skipped</span>
            <span class="badge bg-secondary"><?= $results['total'] ?> Total</span>
        </div>
    </div>
    <div class="card-body">
        <?php if ($results['imported'] > 0): ?>
            <div class="alert alert-success">
                Successfully imported <?= $results['imported'] ?> out of <?= $results['total'] ?> rows.
            </div>
        <?php endif; ?>
        <?php if ($results['skipped'] > 0): ?>
            <div class="alert alert-warning">
                <?= $results['skipped'] ?> rows were skipped. See details below.
            </div>
        <?php endif; ?>

        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-sm table-custom">
                <thead>
                    <tr>
                        <th style="width: 80px;">Row #</th>
                        <th style="width: 100px;">Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results['rows'] as $r): ?>
                    <tr>
                        <td><?= $r['row'] ?></td>
                        <td>
                            <?php if ($r['status'] === 'Imported'): ?>
                                <span class="badge bg-success">Imported</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Skipped</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($r['reason']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('csvFileInput').addEventListener('change', function(e) {
    var file = e.target.files[0];
    var btn = document.getElementById('importBtn');
    var preview = document.getElementById('csvPreview');
    var table = document.getElementById('previewTable');

    if (!file) {
        btn.disabled = true;
        preview.style.display = 'none';
        return;
    }

    var reader = new FileReader();
    reader.onload = function(ev) {
        var text = ev.target.result;
        var lines = text.split(/\r?\n/).filter(function(l) { return l.trim() !== ''; });
        if (lines.length < 2) {
            btn.disabled = true;
            preview.style.display = 'none';
            alert('CSV must have at least a header row and one data row.');
            return;
        }

        var html = '<thead><tr>';
        var headers = parseCSVLine(lines[0]);
        for (var h = 0; h < headers.length; h++) {
            html += '<th class="small" style="white-space:nowrap;">' + escapeHtml(headers[h]) + '</th>';
        }
        html += '</tr></thead><tbody>';

        var maxRows = Math.min(lines.length, 6);
        for (var r = 1; r < maxRows; r++) {
            html += '<tr>';
            var cols = parseCSVLine(lines[r]);
            for (var c = 0; c < headers.length; c++) {
                html += '<td class="small">' + escapeHtml(cols[c] || '') + '</td>';
            }
            html += '</tr>';
        }
        if (lines.length > 6) {
            html += '<tr><td colspan="' + headers.length + '" class="text-muted text-center small">... and ' + (lines.length - 6) + ' more rows</td></tr>';
        }
        html += '</tbody>';

        table.innerHTML = html;
        preview.style.display = 'block';
        btn.disabled = false;
    };
    reader.readAsText(file);
});

function parseCSVLine(line) {
    var result = [];
    var current = '';
    var inQuotes = false;
    for (var i = 0; i < line.length; i++) {
        var ch = line[i];
        if (inQuotes) {
            if (ch === '"') {
                if (i + 1 < line.length && line[i + 1] === '"') {
                    current += '"';
                    i++;
                } else {
                    inQuotes = false;
                }
            } else {
                current += ch;
            }
        } else {
            if (ch === '"') {
                inQuotes = true;
            } else if (ch === ',') {
                result.push(current.trim());
                current = '';
            } else {
                current += ch;
            }
        }
    }
    result.push(current.trim());
    return result;
}

function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
</script>
<?= $this->endSection() ?>
