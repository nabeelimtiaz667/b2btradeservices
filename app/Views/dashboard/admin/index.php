<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">Lead Management Dashboard</h1>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-4">
        <a href="<?= base_url('leads/all') ?>" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                    </svg>
                </div>
                <div class="stat-value"><?= $stats['total_leads'] ?? 0 ?></div>
                <div class="stat-label">Total Leads</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="<?= base_url('leads/buyer') ?>" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(45deg, #0d6efd, #6ea8fe);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                    </svg>
                </div>
                <div class="stat-value"><?= $stats['buyer_leads'] ?? 0 ?></div>
                <div class="stat-label">Buyer Leads</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="<?= base_url('leads/supplier') ?>" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(45deg, #198754, #75b798);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M3 2.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0v.006c0 .07 0 .27-.038.494H15a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 14.5V7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h2.038A2.968 2.968 0 0 1 3 2.506V2.5zm1.068.5H7v-.5a1.5 1.5 0 1 0-3 0c0 .085.002.274.045.43a.522.522 0 0 0 .023.07zM9 3h2.932a.56.56 0 0 0 .023-.07c.043-.156.045-.345.045-.43a1.5 1.5 0 0 0-3 0V3z"/>
                    </svg>
                </div>
                <div class="stat-value"><?= $stats['supplier_leads'] ?? 0 ?></div>
                <div class="stat-label">Supplier Leads</div>
            </div>
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header">
                <h5>Regional Lead Distribution</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php
                    $regionIcons = [
                        'China' => '🇨🇳',
                        'United States' => '🇺🇸',
                        'Europe' => '🇪🇺',
                        'Africa' => '🌍',
                        'UAE' => '🇦🇪',
                        'Caucasian Region' => '🏔️',
                    ];
                    foreach (($stats['regional_counts'] ?? []) as $region => $count): ?>
                    <div class="col-md-4 col-lg-2">
                        <a href="<?= base_url('leads/all?region=' . urlencode($region)) ?>" class="text-decoration-none">
                            <div class="text-center p-3 rounded" style="background: #f8f9fa; transition: all 0.3s;" onmouseover="this.style.background='#e9ecef'" onmouseout="this.style.background='#f8f9fa'">
                                <div style="font-size: 28px; margin-bottom: 5px;"><?= $regionIcons[$region] ?? '🌐' ?></div>
                                <div style="font-size: 24px; font-weight: 700; color: var(--primary-dark);"><?= $count ?></div>
                                <div style="font-size: 12px; color: #6c757d;"><?= esc($region) ?></div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <a href="<?= base_url('leads/supplier?membership_level=free') ?>" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(45deg, #6c757d, #adb5bd);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                </div>
                <div class="stat-value"><?= $stats['free_suppliers'] ?? 0 ?></div>
                <div class="stat-label">Free Suppliers</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="<?= base_url('leads/buyer?membership_level=free') ?>" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(45deg, #6c757d, #adb5bd);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5z"/></svg>
                </div>
                <div class="stat-value"><?= $stats['free_buyers'] ?? 0 ?></div>
                <div class="stat-label">Free Buyers</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="<?= base_url('leads/supplier?membership_level=silver,gold,platinum,vip') ?>" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(45deg, #ffc107, #ffcd39);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5z"/></svg>
                </div>
                <div class="stat-value"><?= $stats['premium_suppliers'] ?? 0 ?></div>
                <div class="stat-label">Premium Suppliers</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="<?= base_url('leads/buyer?membership_level=silver,gold,platinum,vip') ?>" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(45deg, #ffc107, #ffcd39);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5z"/></svg>
                </div>
                <div class="stat-value"><?= $stats['premium_buyers'] ?? 0 ?></div>
                <div class="stat-label">Premium Buyers</div>
            </div>
        </a>
    </div>
</div>

<div class="card card-custom mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">Supplier Lead Statistics <span id="supplier-total" style="font-size:14px; font-weight:400; color:#6c757d;"></span></h5>
        <div class="btn-group btn-group-sm" id="supplier-btn-group">
            <button class="btn btn-outline-primary active" data-days="7" onclick="fetchChart('supplier', 7, this)">7 Days</button>
            <button class="btn btn-outline-primary" data-days="15" onclick="fetchChart('supplier', 15, this)">15 Days</button>
            <button class="btn btn-outline-primary" data-days="30" onclick="fetchChart('supplier', 30, this)">30 Days</button>
            <button class="btn btn-outline-primary" data-days="custom" onclick="toggleDateRange('supplier', this)">Date Range</button>
        </div>
    </div>
    <div class="card-body">
        <div id="supplier-date-range" style="display:none;" class="mb-3">
            <div class="d-flex gap-2 align-items-center">
                <input type="date" id="supplier-from" class="form-control form-control-sm" style="max-width:160px;">
                <span>to</span>
                <input type="date" id="supplier-to" class="form-control form-control-sm" style="max-width:160px;">
                <button class="btn btn-sm btn-primary" onclick="fetchCustomChart('supplier')">Go</button>
            </div>
        </div>
        <div style="position:relative; height:280px;">
            <div id="supplier-loading" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
            <canvas id="supplierChart"></canvas>
        </div>
    </div>
</div>

<div class="card card-custom mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">Buyer Lead Statistics <span id="buyer-total" style="font-size:14px; font-weight:400; color:#6c757d;"></span></h5>
        <div class="btn-group btn-group-sm" id="buyer-btn-group">
            <button class="btn btn-outline-primary active" data-days="7" onclick="fetchChart('buyer', 7, this)">7 Days</button>
            <button class="btn btn-outline-primary" data-days="15" onclick="fetchChart('buyer', 15, this)">15 Days</button>
            <button class="btn btn-outline-primary" data-days="30" onclick="fetchChart('buyer', 30, this)">30 Days</button>
            <button class="btn btn-outline-primary" data-days="custom" onclick="toggleDateRange('buyer', this)">Date Range</button>
        </div>
    </div>
    <div class="card-body">
        <div id="buyer-date-range" style="display:none;" class="mb-3">
            <div class="d-flex gap-2 align-items-center">
                <input type="date" id="buyer-from" class="form-control form-control-sm" style="max-width:160px;">
                <span>to</span>
                <input type="date" id="buyer-to" class="form-control form-control-sm" style="max-width:160px;">
                <button class="btn btn-sm btn-primary" onclick="fetchCustomChart('buyer')">Go</button>
            </div>
        </div>
        <div style="position:relative; height:280px;">
            <div id="buyer-loading" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
            <canvas id="buyerChart"></canvas>
        </div>
    </div>
</div>

<script>
var chartInstances = {};
var chartBaseUrl = '<?= base_url("dashboard/chart-data") ?>';

function formatLabel(dateStr) {
    var d = new Date(dateStr + 'T00:00:00');
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return months[d.getMonth()] + ' ' + String(d.getDate()).padStart(2, '0');
}

function formatFullDate(dateStr) {
    var d = new Date(dateStr + 'T00:00:00');
    var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
}

function renderChart(canvasId, data, colorStart, colorEnd, label) {
    if (chartInstances[canvasId]) {
        chartInstances[canvasId].destroy();
    }
    var ctx = document.getElementById(canvasId).getContext('2d');
    var gradient = ctx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, colorStart);
    gradient.addColorStop(1, colorEnd);
    var hoverGradient = ctx.createLinearGradient(0, 0, 0, 280);
    hoverGradient.addColorStop(0, colorStart);
    hoverGradient.addColorStop(0.6, colorEnd);

    var labels = data.map(function(d) { return formatLabel(d.date); });
    var counts = data.map(function(d) { return d.count; });
    var rawDates = data.map(function(d) { return d.date; });
    var maxCount = Math.max.apply(null, counts.concat([1]));

    chartInstances[canvasId] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: counts,
                backgroundColor: gradient,
                hoverBackgroundColor: hoverGradient,
                borderRadius: 6,
                borderSkipped: false,
                barPercentage: data.length > 15 ? 0.85 : 0.7,
                categoryPercentage: data.length > 15 ? 0.9 : 0.8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleFont: { size: 13, weight: '600' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(items) {
                            return formatFullDate(rawDates[items[0].dataIndex]);
                        },
                        label: function(item) {
                            return item.raw + (item.raw === 1 ? ' lead' : ' leads');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: maxCount <= 10 ? 1 : undefined,
                        precision: 0,
                        color: '#6c757d',
                        font: { size: 12 }
                    },
                    grid: { color: 'rgba(0,0,0,0.06)', drawBorder: false },
                    border: { display: false }
                },
                x: {
                    ticks: {
                        color: '#6c757d',
                        font: { size: data.length > 20 ? 9 : 11 },
                        maxRotation: data.length > 15 ? 45 : 0,
                        autoSkip: data.length > 20,
                        maxTicksLimit: 20
                    },
                    grid: { display: false },
                    border: { display: false }
                }
            },
            animation: {
                duration: 800,
                easing: 'easeOutQuart'
            }
        }
    });
}

function fetchChart(type, days, btnEl) {
    var group = document.getElementById(type + '-btn-group');
    group.querySelectorAll('.btn').forEach(function(b) { b.classList.remove('active'); });
    if (btnEl) btnEl.classList.add('active');
    document.getElementById(type + '-date-range').style.display = 'none';

    var loading = document.getElementById(type + '-loading');
    loading.style.display = 'flex';

    var canvasId = type + 'Chart';
    var chartLabel = type === 'supplier' ? 'Supplier Leads' : 'Buyer Leads';
    var colors = type === 'supplier' ? ['#198754', '#75b798'] : ['#0d6efd', '#6ea8fe'];

    fetch(chartBaseUrl + '?type=' + type + '&days=' + days)
        .then(function(r) { if (!r.ok) throw new Error(r.status); return r.json(); })
        .then(function(json) {
            loading.style.display = 'none';
            document.getElementById(type + '-total').textContent = '(' + json.total + ' total)';
            renderChart(canvasId, json.data, colors[0], colors[1], chartLabel);
        })
        .catch(function() {
            loading.style.display = 'none';
            document.getElementById(type + '-total').textContent = '(error loading)';
        });
}

function toggleDateRange(type, btnEl) {
    var el = document.getElementById(type + '-date-range');
    var isHidden = el.style.display === 'none';
    el.style.display = isHidden ? 'block' : 'none';
    if (isHidden && btnEl) {
        var group = document.getElementById(type + '-btn-group');
        group.querySelectorAll('.btn').forEach(function(b) { b.classList.remove('active'); });
        btnEl.classList.add('active');
    }
}

function fetchCustomChart(type) {
    var from = document.getElementById(type + '-from').value;
    var to = document.getElementById(type + '-to').value;
    if (!from || !to) return;

    var loading = document.getElementById(type + '-loading');
    loading.style.display = 'flex';

    var canvasId = type + 'Chart';
    var chartLabel = type === 'supplier' ? 'Supplier Leads' : 'Buyer Leads';
    var colors = type === 'supplier' ? ['#198754', '#75b798'] : ['#0d6efd', '#6ea8fe'];

    fetch(chartBaseUrl + '?type=' + type + '&from=' + from + '&to=' + to)
        .then(function(r) { if (!r.ok) throw new Error(r.status); return r.json(); })
        .then(function(json) {
            loading.style.display = 'none';
            document.getElementById(type + '-total').textContent = '(' + json.total + ' total)';
            renderChart(canvasId, json.data, colors[0], colors[1], chartLabel);
        })
        .catch(function() {
            loading.style.display = 'none';
            document.getElementById(type + '-total').textContent = '(error loading)';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    fetchChart('supplier', 7, document.querySelector('#supplier-btn-group .btn[data-days="7"]'));
    fetchChart('buyer', 7, document.querySelector('#buyer-btn-group .btn[data-days="7"]'));
});
</script>
<?= $this->endSection() ?>
