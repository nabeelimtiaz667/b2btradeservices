<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">Submission #<?= $submission['id'] ?></h1>
    <a href="<?= base_url('dashboard/submissions') ?>" class="btn btn-outline-secondary">Back to Submissions</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Submission Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 180px;">Form Type</th>
                        <td><span class="badge bg-info"><?= esc(ucwords(str_replace('_', ' ', $submission['form_type']))) ?></span></td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td><?= esc($submission['name']) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><a href="mailto:<?= esc($submission['email']) ?>"><?= esc($submission['email']) ?></a></td>
                    </tr>
                    <?php if (!empty($submission['phone'])): ?>
                    <tr>
                        <th>Phone</th>
                        <td>
                            <?= esc($submission['phone']) ?>
                            <?php if (!empty($submission['whatsapp'])): ?>
                                <span class="badge bg-success ms-1"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/whatsapp.svg" width="12" style="filter:invert(1)"> WhatsApp</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($submission['country_id']) && !empty($country)): ?>
                    <tr>
                        <th>Country</th>
                        <td><?= esc($country['name']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($submission['partnership'])): ?>
                    <tr>
                        <th>Partnership</th>
                        <td>
                            <?php
                            $partnerships = json_decode($submission['partnership'], true);
                            if (is_array($partnerships)) {
                                foreach ($partnerships as $p) {
                                    echo '<span class="badge bg-primary me-1 mb-1">' . esc($p) . '</span>';
                                }
                            } else {
                                echo esc($submission['partnership']);
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($submission['company'])): ?>
                    <tr>
                        <th>Company</th>
                        <td><?= esc($submission['company']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($submission['industry'])): ?>
                    <tr>
                        <th>Industry</th>
                        <td><?= esc($submission['industry']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($submission['quantity'])): ?>
                    <tr>
                        <th>Quantity</th>
                        <td><?= esc($submission['quantity']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($submission['message'])): ?>
                    <tr>
                        <th>Message</th>
                        <td><?= nl2br(esc($submission['message'])) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Source Page</th>
                        <td><?= esc($submission['source_page'] ?? '-') ?></td>
                    </tr>
                    <?php if (!empty($submission['source_id'])): ?>
                    <tr>
                        <th>Source ID</th>
                        <td><?= esc($submission['source_id']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($submission['form_data'])): ?>
                    <?php $fdata = json_decode($submission['form_data'], true); if (is_array($fdata)): ?>
                    <tr><td colspan="2"><hr class="my-1"></td></tr>
                    <?php foreach ($fdata as $fkey => $fval): ?>
                    <?php if (empty($fval)) continue; ?>
                    <tr>
                        <th><?= esc(ucwords(str_replace('_', ' ', $fkey))) ?></th>
                        <td><?= esc($fval) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <?php endif; ?>
                    <tr>
                        <th>Submitted</th>
                        <td><?= date('F d, Y h:i A', strtotime($submission['created_at'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Update Status</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('dashboard/submissions/update-status/' . $submission['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <select name="status" class="form-select">
                            <option value="new" <?= $submission['status'] === 'new' ? 'selected' : '' ?>>New</option>
                            <option value="read" <?= $submission['status'] === 'read' ? 'selected' : '' ?>>Read</option>
                            <option value="replied" <?= $submission['status'] === 'replied' ? 'selected' : '' ?>>Replied</option>
                            <option value="closed" <?= $submission['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <a href="<?= base_url('dashboard/submissions/delete/' . $submission['id']) ?>" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete this submission?')">Delete Submission</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
