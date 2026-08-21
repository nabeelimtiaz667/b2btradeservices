<?php

/**
 * Shared homepage lead-capture form -- the same step-1 fields as the popup
 * CTA (partials/lead-popup-modal.php), reused inline across every homepage
 * form slot instead of each slot having its own (previously inconsistent,
 * mostly wrong) form. Submits to the same LeadCapture::capture() endpoint
 * via the same AJAX flow, driven by assets/js/homepage-lead-forms.js.
 *
 * $idPrefix must be unique per instance on the page -- there are multiple
 * copies of this partial on the homepage, and id attributes must not repeat.
 */
$idPrefix = $idPrefix ?? 'lead';
$defaultRadio = $defaultRadio ?? 'supplier';
?>
<form class="lead-capture-inline-form" data-action="<?= base_url('lead/capture') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="phone_code" class="lead-capture-phone-code" value="">
    <div class="lead-capture-error" style="display:none; color:#dc3545; font-size:13px; margin-bottom:10px;"></div>
    <div class="filter-group">
        <label class="radio-label">
            <input type="radio" name="user_type" value="supplier" <?= $defaultRadio === 'supplier' ? 'checked' : '' ?>>
            <span class="radio"></span>
            Supplier
        </label>
        <label class="radio-label">
            <input type="radio" name="user_type" value="buyer" <?= $defaultRadio === 'buyer' ? 'checked' : '' ?>>
            <span class="radio"></span>
            Buyer
        </label>
    </div>
    <div class="form-input mt-3">
        <input type="text" name="name" placeholder="Name*" required>
    </div>
    <div class="form-input mt-3">
        <input type="email" name="email" placeholder="Email*" required>
    </div>
    <div class="form-input mt-3">
        <input type="tel" name="phone" class="phone lead-capture-phone" placeholder="Phone number*" required>
    </div>
    <div class="whatsapp-checkbox">
        <input type="checkbox" name="whatsapp" id="<?= esc($idPrefix) ?>_whatsapp">
        <label for="<?= esc($idPrefix) ?>_whatsapp">Whatsapp<img
                src="<?= base_url('assets/images/whatsapp-icon.svg') ?>" width="15px"></label>
    </div>
    <div class="submit-btn-gradient mt-3">
        <button type="submit" class="gradeint-cta">Get Started</button>
    </div>
</form>