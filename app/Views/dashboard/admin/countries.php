<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">Countries &amp; Phone Codes</h1>
    <form method="post" action="<?= base_url('dashboard/countries/sync') ?>" id="countrySyncForm"
        onsubmit="return confirmCountrySync(this)">
        <?= csrf_field() ?>
        <button type="submit" id="countrySyncBtn" class="btn"
            style="background: var(--primary-gradient); color: #fff;">Update Now</button>
    </form>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-body">
        <p class="text-muted mb-3">
            <strong><?= $lastUpdated ? 'Last updated: ' . esc($lastUpdated) : 'Never synced yet.' ?></strong><br>
            <!-- <span class="small">
                Sourced from a free, community-maintained country dataset
                (<a href="https://github.com/mledoze/countries" target="_blank" rel="noopener noreferrer">mledoze/countries</a>)
                plus <a href="https://flagcdn.com" target="_blank" rel="noopener noreferrer">flagcdn.com</a> for flags.
                This list isn't editable here -- click "Update Now" to pull the latest version.
            </span> -->
        </p>
        <div class="mb-3">
            <input type="text" class="form-control" id="countrySearch" placeholder="Search countries..."
                onkeyup="filterTable('countrySearch', 'countriesTable')">
        </div>
        <div class="table-responsive">
            <table class="table table-custom" id="countriesTable">
                <thead>
                    <tr>
                        <th>Flag</th>
                        <th>Country</th>
                        <th>Code</th>
                        <th>Phone Code</th>
                        <th>Region</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($countries as $c): ?>
                    <tr>
                        <td><img src="<?= esc($c['flag']) ?>" alt="<?= esc($c['name']) ?>" width="24"
                                style="border-radius:3px;" onerror="this.style.display='none'"></td>
                        <td><?= esc($c['name']) ?></td>
                        <td><?= esc($c['code']) ?></td>
                        <td><?= esc($c['phone_code'] ?: '—') ?></td>
                        <td><?= esc($c['region'] ?: '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmCountrySync(form) {
    if (!confirm(
            'Refresh the country list from the source dataset now? This fetches the latest data and may take a few seconds.'
            )) {
        return false;
    }
    var btn = document.getElementById('countrySyncBtn');
    btn.disabled = true;
    btn.textContent = 'Updating...';
    return true;
}

function filterTable(inputId, tableId) {
    var input = document.getElementById(inputId);
    var filter = input.value.toLowerCase();
    var table = document.getElementById(tableId);
    var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        var text = rows[i].textContent.toLowerCase();
        rows[i].style.display = text.indexOf(filter) > -1 ? '' : 'none';
    }
}
</script>
<?= $this->endSection() ?>