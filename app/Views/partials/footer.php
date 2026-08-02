<footer>
    <div class="border-top-footer"></div>

    <div class="footer-upper-links">
        <div class="container">
            <div class="row">
                <div class="footer-links-col">
                    <p class="link_head text-white">Customer Services</p>
                    <ul>
                        <li><a href="<?= base_url('contact') ?>"> Contact Us</a></li>
                        <li><a href="<?= base_url('privacy-policy') ?>"> Privacy Policy</a></li>
                        <li><a href="<?= base_url('terms-and-conditions') ?>"> Terms & Conditions</a></li>
                        <li><a href="<?= base_url('refund-policy') ?>"> Refund Policy</a></li>
                        <li><a href="<?= base_url('user-guide') ?>"> User Guide</a></li>
                        <li><a href="<?= base_url('banned-keywords-and-illegal-products-policy') ?>"> Banned Keywords
                                and Illegal Products</a></li>
                    </ul>
                </div>
                <div class="footer-links-col">
                    <p class="link_head text-white">About Us</p>
                    <ul>
                        <li><a href="<?= base_url('about-us') ?>"> About Company</a></li>
                        <li><a href="<?= base_url('become-our-agent-partner') ?>"> Become Our Agent Partner</a></li>
                        <li><a href="<?= base_url('tradeshow-marketing-services') ?>"> Tradeshow Marketing Service</a>
                        </li>
                        <li><a href="<?= base_url('success-stories') ?>"> Success Stories</a></li>
                    </ul>
                </div>

                <div class="footer-links-col">
                    <p class="link_head text-white">Sell on b2btradeservices.com</p>
                    <ul>
                        <li><a href="<?= base_url('premium-services') ?>"> Premium Services</a></li>
                        <li><a href="<?= base_url('premium-services/starter-package') ?>"> Starter Package</a></li>
                        <li><a href="<?= base_url('premium-services/gold-package') ?>"> Gold Package</a></li>
                        <li><a href="<?= base_url('premium-services/platinum-package') ?>"> Platinum Package</a></li>
                        <li><a href="<?= base_url('premium-services/vip-package') ?>"> VIP Package</a></li>
                    </ul>
                </div>
                <div class="footer-links-col">
                    <p class="link_head text-white">Get In Touch</p>
                    <ul>
                        <li><a href="<?= base_url('contact') ?>">
                                <?= esc($siteSettings['contact_address'] ?? '1001 S MAIN STREET, STE 500 - Kalispell, MONTANA, UNITED STATES 59901') ?></a>
                        </li>
                        <?php if (!empty($siteSettings['contact_phone'])): ?>
                        <li><a href="tel:<?= esc($siteSettings['contact_phone']) ?>"><i class="fas fa-phone"></i>
                                <?= esc($siteSettings['contact_phone']) ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom-part">
        <div class="container">
            <div class="row align-items-center">
                <div class="footer-bottom-col-1">
                    <p class="text-white mb-0">Mail Us</p>
                    <?php $contactEmail = $siteSettings['contact_email'] ?? ''; ?>
                    <?php if ($contactEmail): ?>
                    <a href="mailto:<?= esc($contactEmail) ?>"><?= esc($contactEmail) ?></a>
                    <?php else: ?>
                    <a href="mailto:info@b2btradeservices.com">info@b2btradeservices.com</a>
                    <a href="mailto:support@b2btradeservices.com">support@b2btradeservices.com</a>
                    <?php endif; ?>
                    <div class="social-icons mt-2 d-flex gap-2 align-items-center d-none">
                        <a href="#"><img src="<?= base_url('assets/images/fb-icon.svg') ?>"></a>
                        <a href="#"><img src="<?= base_url('assets/images/insta-icon.svg') ?>"></a>
                    </div>
                </div>
                <div class="footer-bottom-col-2">
                    <p class="text-white mb-0 footer-disclaimer">
                        All content submitted by users including images, products, pricing, promotions and company
                        information is the sole responsibility of the respective users. b2btradeservices.com is not
                        responsible for the accuracy, authenticity or legality of any information posted by users on the
                        platform. <br><br>
                        The use or upload of images containing watermarks or any intellectual property markings is
                        strictly prohibited.<br><br>
                        If you have any concerns or complaints about content published on the platform, please contact
                        us at <a href="mailto:info@b2btradeservices.com">info@b2btradeservices.com</a>.

                    </p>
                </div>
            </div>

            <div>
                <p class="text-center text-white mb-0 mt-5">
                    <?= esc($siteSettings['footer_text'] ?? 'Copyrights © 2026 b2btradeservices.com All Rights Reserved.') ?>
                </p>
            </div>

            <div class="d-none">
                <p class="text-center text-white mb-0" style="margin-top: 10px; font-size: 10px;">Project Developed By:
                    <a style="color: #fff;" target="_blank" href="https://designsctrl.net/">Designsctrl.com</a>
                </p>
            </div>

        </div>
    </div>
</footer>

<div class="register-your-company text-white">
    Register Your Company
</div>

<div id="popup" style="display: none;">
    <div class="b2b-top-form">
        <button id="closePopup">X</button>
        <h2 class="text-center">Register Your Company</h2>
        <h3>Join the World's Fastest Growing B2B Network</h3>
        <form action="<?= base_url('register') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="filter-group">
                <label class="radio-label">
                    <input type="radio" name="role" value="supplier" checked onchange="toggleFields(this)">
                    <span class="radio"></span>
                    Supplier
                </label>
                <label class="radio-label">
                    <input type="radio" name="role" value="buyer" onchange="toggleFields(this)">
                    <span class="radio"></span>
                    Buyer
                </label>
            </div>
            <div class="form-input mt-3">
                <input type="text" name="name" placeholder="Name*" required>
            </div>
            <div class="dual-input">
                <div class="form-input">
                    <input type="email" name="email" placeholder="Email*" required>
                </div>
                <div class="form-input password-input">
                    <input type="password" name="password" class="password" placeholder="Password*" required>
                    <i class="eye" onclick="togglePassword()">
                        <svg style="fill: #DBDBDB" class="eye-icon" height="20" viewBox="0 0 24 24" width="20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd"
                                d="m12 4.5c-5 0-9.27 3.11-11 7.5 1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z">
                            </path>
                        </svg>
                    </i>
                </div>
            </div>
            <div class="form-input">
                <input type="tel" name="phone" class="phone" placeholder="Phone number*" required>
            </div>
            <div class="whatsapp-checkbox">
                <input type="checkbox" name="whatsapp" id="popup_whatsapp"><label for="popup_whatsapp">Whatsapp<img
                        src="<?= base_url('assets/images/whatsapp-icon.svg') ?>" width="15px"></label>
            </div>
            <div class="form-input selling-field">
                <input type="text" name="selling_products" placeholder="Selling Products*" required>
            </div>
            <div class="form-input buying-field" style="display:none;">
                <input type="text" name="buying_products" placeholder="Buying Products*">
            </div>
            <div class="radio-join d-flex gap-2 align-items-start">
                <input type="checkbox" name="OPT_IN" id="popup_OPT_IN" required><label for="popup_OPT_IN"><span
                        style="color: #0F9EA5;">*</span>By joining, I agree to terms of use, privacy policy, IPR and
                    agree to receive emails related to our services.</label>
            </div>
            <div class="submit-btn-gradient mt-3">
                <button type="submit" class="gradeint-cta">Join Now</button>
            </div>
        </form>
    </div>
</div>

<div id="overlay"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
</div>

<div class="thankyou-popup" id="thankyouPopup">

    <div class="popup-box">

        <button id="closePopup2">X</button>

        <h2>Thank You!</h2>
        <p>Your submission has been received.</p>
        <div class="submit-btn-gradient mt-3">
            <button type="submit" class="gradeint-cta"><a href="#">Home</a></button>
        </div>

    </div>

</div>

<script>
function openPopup() {
    document.getElementById("thankyouPopup").style.display = "flex";
}

function closePopup() {
    document.getElementById("thankyouPopup").style.display = "none";
}
</script>
<script>
document.getElementById("closePopup2").onclick = function() {
    document.getElementById("thankyouPopup").style.display = "none";
};
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>

<script>
if ($('.banner-slider').length) {
    $('.banner-slider').slick({
        autoplay: true,
        autoplaySpeed: 3000,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        arrows: false,
        pauseOnHover: true,
        fade: true,
        cssEase: 'ease-in-out'
    });
}
if ($('.category-slider').length) {
    $('.category-slider').slick({
        autoplay: true,
        autoplaySpeed: 5000,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        arrows: false
    });
}
if ($('.flag-slider').length) {
    $('.flag-slider').slick({
        autoplay: true,
        autoplaySpeed: 5000,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        arrows: false
    });
}
if ($('.success-stories-slider').length) {
    $('.success-stories-slider').slick({
        autoplay: true,
        autoplaySpeed: 5000,
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        arrows: false
    });
}
if ($('.top-supplier-logo-slider').length) {
    $('.top-supplier-logo-slider').slick({
        autoplay: true,
        autoplaySpeed: 5000,
        slidesToShow: 6,
        slidesToScroll: 1,
        dots: false,
        arrows: false,
        pauseOnHover: true,
        responsive: [{
                breakpoint: 992,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 3
                }
            }
        ]
    });
}
</script>
<script>
document.addEventListener('error', function(e) {
    var img = e.target;
    if (img.tagName !== 'IMG') return;
    if (img.dataset.fallbackApplied) return;
    var src = img.getAttribute('src') || '';
    if (src.indexOf('/flags/') !== -1) return;
    if (img.closest('.supplier-profile-banner, .supplier-profile-slider')) return;
    img.dataset.fallbackApplied = '1';
    img.src = 'https://img.freepik.com/free-vector/illustration-gallery-icon_53876-27002.jpg';
}, true);
</script>
<!-- Google tag (gtag.js) -->
<!-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-L52TR0D4JK"></script>
<script>
window.dataLayer = window.dataLayer || [];

function gtag() {
    dataLayer.push(arguments);
}
gtag('js', new Date());

gtag('config', 'G-L52TR0D4JK');
</script> -->