<style>
#leadPopupModal .modal-content {
    border: none;
    border-radius: 16px;
    overflow: hidden
}

#leadPopupModal .lead-popup-body {
    padding: 36px 32px
}

#leadPopupModal h3 {
    font-weight: 700;
    color: #0A504F;
    margin-bottom: 8px
}

#leadPopupModal .lead-popup-sub {
    color: #666;
    font-size: 14px;
    margin-bottom: 22px
}

#leadPopupModal .lead-popup-error {
    background: rgba(220, 53, 69, 0.1);
    border-left: 3px solid #dc3545;
    padding: 10px 14px;
    border-radius: 6px;
    margin-bottom: 16px;
    font-size: 13px;
    color: #333;
    display: none
}

#leadPopupModal .lead-popup-step {
    display: none
}

#leadPopupModal .lead-popup-step.active {
    display: block
}

#leadPopupModal .lead-popup-result-icon {
    font-size: 36px;
    color: #15A2A0;
    margin-bottom: 14px
}

#leadPopupModal .lead-popup-later {
    background: none;
    border: none;
    color: #666;
    font-size: 13px;
    text-decoration: underline;
    cursor: pointer;
    display: block;
    margin: 14px auto 0;
}

#leadPopupForm input {
    border: 1px solid #DBDBDB;
}
</style>

<div class="modal fade" id="leadPopupModal" tabindex="-1" aria-labelledby="leadPopupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0 lead-popup-body">

                <!-- Step 1: capture -->
                <div class="lead-popup-step active" data-step="capture">
                    <h3 id="leadPopupHeading">Join B2B Trade Services</h3>
                    <p class="lead-popup-sub" id="leadPopupSubtext">Leave your details and start connecting with trade
                        partners.</p>
                    <div class="lead-popup-error" id="leadPopupError"></div>

                    <form id="leadPopupForm" data-action="<?= base_url('lead/capture') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="phone_code" id="leadPopupPhoneCode" value="">

                        <div class="filter-group">
                            <label class="radio-label">
                                <input type="radio" name="user_type" value="supplier" id="leadPopupTypeSupplier">
                                <span class="radio"></span>
                                Supplier
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="user_type" value="buyer" id="leadPopupTypeBuyer">
                                <span class="radio"></span>
                                Buyer
                            </label>
                        </div>
                        <div class="form-input mt-3">
                            <input type="text" name="name" placeholder="Name*" autocomplete="name" required>
                        </div>
                        <div class="form-input mt-3">
                            <input type="email" name="email" placeholder="Email*" autocomplete="email" required>
                        </div>
                        <div class="form-input mt-3">
                            <input type="tel" id="leadPopupPhone" name="phone" placeholder="Phone number*" required>
                        </div>
                        <div class="whatsapp-checkbox">
                            <input type="checkbox" id="leadPopupWhatsapp" name="whatsapp">
                            <label for="leadPopupWhatsapp"><img src="<?= base_url('assets/images/whatsapp-icon.svg') ?>"
                                    width="15px">Whatsapp</label>
                        </div>
                        <div class="submit-btn-gradient mt-3">
                            <button type="submit" class="gradeint-cta" id="leadPopupSubmitBtn">Get Started</button>
                        </div>
                    </form>
                </div>

                <!-- Step 2: check your email / already verified -->
                <div class="lead-popup-step" data-step="result">
                    <div class="text-center">
                        <div class="lead-popup-result-icon"><i class="fas fa-envelope-circle-check"></i></div>
                        <h3 id="leadPopupResultHeading">Check your email</h3>
                        <p class="lead-popup-sub" id="leadPopupResultMessage">We've sent you a verification link to
                            finish setting up your account.</p>
                        <button type="button" class="lead-popup-later" data-bs-dismiss="modal">Maybe later</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
// Local dev serves the app from a /b2btradeservices subfolder; production
// (per CLAUDE.md) does not. The trigger config's page-matching patterns are
// written against the app-relative path ('/buyers', '/supplier', ...), so
// strip whatever base path CI4 is actually running under before matching --
// otherwise every pattern silently fails to match under a subfolder deploy.
window.LEAD_POPUP_BASE_PATH = '<?= rtrim(parse_url(base_url(), PHP_URL_PATH) ?? '', '/') ?>';
</script>
<script src="<?= base_url('assets/js/lead-popup-triggers.js') ?>"></script>
<script src="<?= base_url('assets/js/lead-popup.js') ?>"></script>