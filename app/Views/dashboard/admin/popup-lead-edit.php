<?= $this->extend('layouts/dashboard') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css" />

<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title"><?= esc($title) ?></h1>
    <a href="<?= base_url('leads/popup') ?>" class="btn btn-sm btn-outline-secondary">Back to Popup Leads</a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= esc(session()->getFlashdata('error')) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card card-custom">
    <div class="card-body">
        <form method="post" action="<?= base_url('leads/popup/edit/' . $lead['id']) ?>" id="popupLeadEditForm">
            <?= csrf_field() ?>
            <input type="hidden" name="phone_code" id="phone_code" value="<?= esc(old('phone_code', $lead['phone_code'] ?? '')) ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px; font-weight:600;">Type</label>
                    <select name="user_type" class="form-select">
                        <option value="buyer" <?= old('user_type', $lead['user_type']) === 'buyer' ? 'selected' : '' ?>>Buyer</option>
                        <option value="supplier" <?= old('user_type', $lead['user_type']) === 'supplier' ? 'selected' : '' ?>>Supplier</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px; font-weight:600;">Status</label>
                    <select name="status" class="form-select">
                        <?php
                        $statusLabels = [
                            'popup_form_filled'  => 'Popup Form Filled',
                            'email_verified'     => 'Email Verified',
                            'account_registered' => 'Account Registered',
                        ];
                        foreach ($statusLabels as $key => $label): ?>
                        <option value="<?= $key ?>" <?= old('status', $lead['status']) === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px; font-weight:600;">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= esc(old('name', $lead['name'])) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px; font-weight:600;">Email</label>
                    <input type="email" class="form-control" value="<?= esc($lead['email']) ?>" readonly disabled title="Email is the identity this lead's verification link proved -- it can't be changed here.">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px; font-weight:600;">Phone</label>
                    <input type="tel" id="phone" name="phone" class="form-control" value="<?= esc(old('phone', $lead['phone'] ?? '')) ?>">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="whatsapp" id="whatsapp" class="form-check-input" value="1" <?= old('whatsapp', $lead['whatsapp'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="whatsapp">Has WhatsApp</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px; font-weight:600;">Assigned Agent</label>
                    <select name="assigned_agent_id" class="form-select">
                        <option value="">Unassigned</option>
                        <?php $currentAgent = old('assigned_agent_id', $lead['assigned_agent_id'] ?? ''); ?>
                        <?php foreach (($agents ?? []) as $agent): ?>
                        <option value="<?= $agent['id'] ?>" <?= (string) $currentAgent === (string) $agent['id'] ? 'selected' : '' ?>><?= esc($agent['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:13px; font-weight:600;">Stage</label>
                    <select name="lead_stage" class="form-select">
                        <?php $currentStage = old('lead_stage', $lead['lead_stage'] ?? 'new'); ?>
                        <?php foreach (($lead_stages ?? []) as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $currentStage === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn" style="background: var(--primary-gradient); color: #fff; padding: 8px 24px;">Save Changes</button>
                <a href="<?= base_url('leads/popup') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Same intlTelInput setup used on the public register/lead-popup forms,
    // adapted for editing an existing number: setNumber() with the stored
    // phone_code+phone re-selects the right country flag instead of always
    // defaulting to US, so saving without touching the phone field doesn't
    // silently overwrite an existing phone_code.
    const input = document.querySelector("#phone");
    let iti = null;
    if (input) {
        iti = window.intlTelInput(input, {
            initialCountry: "us",
            separateDialCode: true,
            preferredCountries: ["us", "gb", "in", "pk"],
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"
        });
        <?php if (!empty($lead['phone_code']) || !empty($lead['phone'])): ?>
        iti.setNumber(<?= json_encode(($lead['phone_code'] ?? '') . ($lead['phone'] ?? '')) ?>);
        <?php endif; ?>
    }

    document.getElementById('popupLeadEditForm').addEventListener('submit', function() {
        if (iti) {
            document.getElementById('phone_code').value = '+' + iti.getSelectedCountryData().dialCode;
        }
    });
</script>
<?= $this->endSection() ?>
