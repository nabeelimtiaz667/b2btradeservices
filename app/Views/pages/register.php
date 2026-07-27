<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<section class="inner-form-banner">
    <div class="inner-form-banner-img">
        <img src="<?= base_url('assets/images/register-img.webp') ?>" class="w-100">
    </div>
    <div class="border-bottom-gradient"></div>
    <div class="form-sec">
        <div class="b2b-logo">
            <img src="<?= base_url('assets/images/logo.svg') ?>" class="w-100">
        </div>
        <h2>Sign Up Now</h2>
        <p>Step into the world of Global Exporters &amp; Importers and Grow your Business 10x Times</p>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post" action="<?= base_url('register') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="phone_code" id="phone_code" value="">
            <div class="filter-group">
                <label class="radio-label">
                    <input type="radio" name="user_type" value="supplier" checked onchange="toggleFields(this)">
                    <span class="radio"></span>
                    Supplier
                </label>
                <label class="radio-label">
                    <input type="radio" name="user_type" value="buyer" onchange="toggleFields(this)">
                    <span class="radio"></span>
                    Buyer
                </label>
            </div>
            <div class="form-input">
                <input type="text" name="name" placeholder="Name*" autocomplete="name" required>
            </div>
            <div class="dual-input">
                <div class="form-input mt-3">
                    <input type="email" name="email" placeholder="Email*" autocomplete="email" required>
                </div>
                <div class="form-input password-input">
                    <input type="password" class="password" name="password" placeholder="Password*" autocomplete="new-password" required>
                    <i class="eye" onclick="togglePassword()">
                        <svg style="fill: #DBDBDB" class="eye-icon" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" d="m12 4.5c-5 0-9.27 3.11-11 7.5 1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"></path>
                        </svg>
                    </i>
                </div>
            </div>
            <div class="form-input">
                <input type="tel" id="phone" name="phone" placeholder="Phone number*" required>
            </div>
            <div class="whatsapp-checkbox">
                <input type="checkbox" id="whatsapp" name="whatsapp">
                <label for="whatsapp"><img src="<?= base_url('assets/images/whatsapp-icon.svg') ?>" width="15px">Whatsapp</label>
            </div>
            <div class="form-input">
                <input type="text" name="company_name" placeholder="Company Name*" value="<?= old('company_name') ?>" required>
            </div>
            <div class="form-input">
                <select class="form-control country-select" required name="country_id">
                    <option value="">Select Country*</option>
                    <?php foreach (($countries ?? []) as $c): ?>
                    <option value="<?= esc($c['id']) ?>"><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-input selling-field">
                <input type="text" name="selling_products" placeholder="Selling Products*">
            </div>
            <div class="form-input buying-field" style="display:none;">
                <input type="text" name="buying_products" placeholder="Buying Products*">
            </div>
            <div class="radio-join d-flex gap-2 align-items-start">
                <input type="checkbox" name="opt_in" id="opt_in" required>
                <label for="opt_in"><span style="color: #0F9EA5;">*</span>By joining. I agree to terms of use, privacy policy, IPR and agree to receive emails related to our services.</label>
            </div>
            <div class="submit-btn-gradient mt-3">
                <button type="submit" class="gradeint-cta">Join Now</button>
            </div>
        </form>
        <!--<p class="mt-4">Or Sign up with</p>-->
        <!--<div class="social-connect">-->
        <!--    <a href="#"><img src="<?= base_url('assets/images/fb-reg-icon.svg') ?>"></a>-->
        <!--    <a href="#"><img src="<?= base_url('assets/images/google-reg-icon.svg') ?>"></a>-->
        <!--</div>-->
        <div class="mt-4">
            <p>Already a member? <a href="<?= base_url('login') ?>" class="reg-a">Sign In</a></p>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const input = document.querySelector("#phone");
    let iti = null;
    if (input) {
        iti = window.intlTelInput(input, {
            initialCountry: "us",
            separateDialCode: true,
            preferredCountries: ["us", "gb", "in", "pk"],
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"
        });
    }
    
    document.querySelector('form').addEventListener('submit', function() {
        if (iti) {
            const phoneCodeInput = document.getElementById('phone_code');
            const selectedCountryData = iti.getSelectedCountryData();
            phoneCodeInput.value = '+' + selectedCountryData.dialCode;
        }
    });

    function toggleFields(radio) {
        const sellingField = document.querySelector('.selling-field');
        const buyingField = document.querySelector('.buying-field');
        const sellingInput = sellingField.querySelector('input');
        const buyingInput = buyingField.querySelector('input');
        
        if (radio.value === 'supplier') {
            sellingField.style.display = 'block';
            buyingField.style.display = 'none';
            sellingInput.required = true;
            buyingInput.required = false;
        } else {
            sellingField.style.display = 'none';
            buyingField.style.display = 'block';
            sellingInput.required = false;
            buyingInput.required = true;
        }
    }

    function togglePassword() {
        const passwordInput = document.querySelector('.password');
        const eyeIcon = document.querySelector('.eye-icon');
        
        if (passwordInput && eyeIcon) {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.fill = '#0F9EA5';
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.fill = '#DBDBDB';
            }
        }
    }
</script>
<?= $this->endSection() ?>
