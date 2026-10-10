<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title"><?= esc($title) ?></h1>
</div>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link" href="<?= base_url('leads/popup') ?>">Leads</a></li>
    <li class="nav-item"><a class="nav-link active" href="<?= base_url('leads/popup/triggers') ?>">Trigger Settings</a></li>
</ul>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= esc(session()->getFlashdata('success')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= esc(session()->getFlashdata('error')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-header"><h5 class="mb-0">Popup Timing</h5></div>
    <div class="card-body">
        <p class="text-muted" style="font-size:13px;">
            Controls when the lead-capture popup appears to visitors who are not logged in.
            Both values are in seconds; enter <strong>0</strong> to turn that case off.
            Once a visitor submits the popup form, it stops appearing for the rest of their session.
        </p>
        <form method="post" action="<?= base_url('leads/popup/triggers') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Show after the page loads (seconds)</label>
                <input type="number" name="popup_first_delay" class="form-control" min="0" step="1" value="<?= esc($values['popup_first_delay']) ?>">
                <div class="form-text">When a visitor opens a page, the popup appears this many seconds later.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Show again while they stay (seconds)</label>
                <input type="number" name="popup_repeat_interval" class="form-control" min="0" step="1" value="<?= esc($values['popup_repeat_interval']) ?>">
                <div class="form-text">If a visitor stays on the page, the popup appears again this many seconds after they close it.</div>
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
