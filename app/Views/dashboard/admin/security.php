<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">Site Security</h1>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-header">
        <h5 class="mb-0">Rate Limiting</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            Each public form below only accepts a limited number of submissions ("N") from the same
            visitor within a fixed time window, to blunt brute-force login attempts and spam. The time
            window is fixed; N is adjustable here without a deploy.
        </p>
        <form method="post" action="<?= base_url('dashboard/security/update') ?>">
            <?= csrf_field() ?>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Form</th>
                            <th>Time Window</th>
                            <th style="width:160px;">N (max submissions)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($forms as $name => $form): ?>
                        <tr>
                            <td><?= esc($form['label']) ?></td>
                            <td><?= $form['window'] >= 60 ? esc(round($form['window'] / 60)) . ' min' : esc($form['window']) . ' sec' ?>
                            </td>
                            <td>
                                <input type="number" name="ratelimit_<?= esc($name) ?>" class="form-control"
                                    min="1" max="1000" value="<?= esc($form['capacity']) ?>">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn mt-3" style="background: var(--primary-gradient); color: #fff;">Save Settings</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
